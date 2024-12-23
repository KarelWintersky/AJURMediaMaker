<?php

namespace AJURMediaMaker\Units;

class OutgoingFile
{
    /**
     * @var string
     */
    public string $uuid;

    /**
     * @var array
     */
    public array $fileinfo;

    /**
     * @var string
     */
    public string $filepath;

    /**
     * @var string
     */
    public string $fast_filepath;

    public function __construct($uuid)
    {
        $this->uuid = $uuid;
        $this->filepath = config('path.storage.outbound') . '/' . $uuid . '.jpg';
        $this->fileinfo = [
            'fn_public' =>  $uuid . '.jpg',
            'mimetype'  =>  'image/jpeg',
            'filesize'  =>  filesize($this->filepath)
        ];
    }

}