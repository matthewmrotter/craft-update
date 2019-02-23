<?php
/**
 * Freeform for Craft
 *
 * @package       Solspace:Freeform
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2008-2017, Solspace, Inc.
 * @link          https://solspace.com/craft/freeform
 * @license       https://solspace.com/software/license-agreement
 */

namespace Solspace\Freeform\Services;

use craft\base\Volume;
use craft\db\Query;
use craft\elements\Asset;
use craft\helpers\Assets;
use craft\records\Asset as AssetRecord;
use craft\web\UploadedFile;
use Solspace\Freeform\Events\Files\UploadEvent;
use Solspace\Freeform\Library\Composer\Components\FieldInterface;
use Solspace\Freeform\Library\Composer\Components\Fields\FileUploadField;
use Solspace\Freeform\Library\FileUploads\FileUploadHandlerInterface;
use Solspace\Freeform\Library\FileUploads\FileUploadResponse;
use Solspace\Freeform\Records\FieldRecord;
use Solspace\Freeform\Records\UnfinalizedFileRecord;

class FilesService extends BaseService implements FileUploadHandlerInterface
{
    const CLEANUP_CACHE_KEY = 'freeform_file_cleanup_cache_key';
    const CACHE_TTL         = 3600; // 1 hour

    const EVENT_BEFORE_UPLOAD = 'beforeUpload';
    const EVENT_AFTER_UPLOAD  = 'afterUpload';

    /** @var array */
    private static $fileUploadFieldIds;

    /**
     * Uploads a file and flags it as "unfinalized"
     * It will be finalized only after the form has been submitted fully
     * All unfinalized files will be deleted after a certain amount of time
     *
     * @param FileUploadField $field
     *
     * @return FileUploadResponse|null
     */
    public function uploadFile(FileUploadField $field)
    {
        if (!$field->getAssetSourceId()) {
            return null;
        }

        $assetService = \Craft::$app->assets;
        $folder       = $assetService->getRootFolderByVolumeId($field->getAssetSourceId());

        if (!$_FILES || !isset($_FILES[$field->getHandle()])) {
            return null;
        }

        $uploadedFileCount = \count($_FILES[$field->getHandle()]['name']);

        $beforeUploadEvent = new UploadEvent($field);
        $this->trigger(self::EVENT_BEFORE_UPLOAD, $beforeUploadEvent);

        if (!$beforeUploadEvent->isValid) {
            return null;
        }

        $uploadedAssetIds = $errors = [];
        for ($i = 0; $i < $uploadedFileCount; $i++) {
            $uploadedFile = UploadedFile::getInstanceByName($field->getHandle() . "[$i]");

            if (!$uploadedFile) {
                continue;
            }

            $asset = $response = null;
            try {
                $filename = Assets::prepareAssetName($uploadedFile->name);
                $asset    = new Asset();

                $asset->tempFilePath           = $uploadedFile->tempName;
                $asset->filename               = $filename;
                $asset->newFolderId            = $folder->id;
                $asset->volumeId               = $folder->volumeId;
                $asset->avoidFilenameConflicts = true;
                $asset->setScenario(Asset::SCENARIO_CREATE);

                $response = \Craft::$app->getElements()->saveElement($asset);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

            if ($response) {
                $assetId = $asset->id;
                $this->markAssetUnfinalized($assetId);

                $uploadedAssetIds[] = $assetId;
            } else if ($asset) {
                $errors = array_merge($errors, $asset->getErrors());
            }
        }

        $this->trigger(self::EVENT_AFTER_UPLOAD, new UploadEvent($field));

        if ($uploadedAssetIds) {
            return new FileUploadResponse($uploadedAssetIds);
        }

        return new FileUploadResponse(null, $errors);
    }

    /**
     * Returns an array of all fields which are of type FILE
     *
     * @return array
     */
    public function getFileUploadFieldIds(): array
    {
        if (null === self::$fileUploadFieldIds) {
            $fileUploadFieldIds = (new Query())
                ->select(['id'])
                ->from(FieldRecord::TABLE)
                ->where(['type' => FieldInterface::TYPE_FILE])
                ->column();

            $fileUploadFieldIds = array_map('intval', $fileUploadFieldIds);

            self::$fileUploadFieldIds = $fileUploadFieldIds;
        }

        return self::$fileUploadFieldIds;
    }

    /**
     * Stores the unfinalized assetId in the database
     * So that it can be deleted later if the form hasn't been finalized
     *
     * @param int $assetId
     */
    public function markAssetUnfinalized($assetId)
    {
        $record          = new UnfinalizedFileRecord();
        $record->assetId = $assetId;
        $record->save(false);
    }

    /**
     * Remove all unfinalized assets which are older than the TTL
     * specified in settings
     *
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function cleanUpUnfinalizedAssets()
    {
        $hasBeenPurgedRecently = \Craft::$app->cache->get(static::CLEANUP_CACHE_KEY);
        if ($hasBeenPurgedRecently) {
            return;
        }

        if (!\Craft::$app->db->tableExists(UnfinalizedFileRecord::TABLE)) {
            return;
        }

        $date = new \DateTime('-180 minutes');

        $assetIds = (new Query())
            ->select(['assetId'])
            ->from(UnfinalizedFileRecord::TABLE)
            ->where(
                '{{%freeform_unfinalized_files}}.[[dateCreated]] < :now',
                ['now' => $date->format('Y-m-d H:i:s')]
            )
            ->column();

        if (!empty($assetIds)) {
            foreach ($assetIds as $assetId) {
                try {
                    $asset = AssetRecord::find()->where(['id' => $assetId])->one();
                    if ($asset) {
                        $asset->delete();
                    }
                } catch (\Exception $e) {
                }

                \Craft::$app->db
                    ->createCommand()
                    ->delete(
                        UnfinalizedFileRecord::TABLE,
                        ['assetId' => $assetId]
                    )
                    ->execute();
            }
        }

        \Craft::$app->cache->set(static::CLEANUP_CACHE_KEY, true, static::CACHE_TTL);
    }

    /**
     * Get a serializable array of asset sources containing
     * their ID, name and type
     *
     * @return array
     */
    public function getAssetSources(): array
    {
        /** @var Volume[] $volumes */
        $volumes      = \Craft::$app->volumes->getAllVolumes();
        $assetSources = [];
        foreach ($volumes as $source) {
            $assetSources[] = [
                'id'   => (int) $source->id,
                'name' => $source->name,
                'type' => 'volume',
            ];
        }

        return $assetSources;
    }

    /**
     * Get a key-value list of asset sources, indexed by ID
     *
     * @return array
     */
    public function getAssetSourceList(): array
    {
        /** @var Volume[] $volumes */
        $volumes      = \Craft::$app->volumes->getAllVolumes();
        $assetSources = [];
        foreach ($volumes as $source) {
            $assetSources[(int) $source->id] = $source->name;
        }

        return $assetSources;
    }

    /**
     * Returns an array of all file kinds
     * [type => [ext, ext, ..]
     * I.e. ["image" => ["gif", "png", "jpg", "jpeg", ..]]
     *
     * @return array
     */
    public function getFileKinds(): array
    {
        $fileKinds = Assets::getFileKinds();

        $returnArray = [];
        foreach ($fileKinds as $kind => $extensions) {
            $returnArray[$kind] = $extensions['extensions'];
        }

        return $returnArray;
    }
}
