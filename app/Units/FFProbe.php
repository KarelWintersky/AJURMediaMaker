<?php

namespace AJURMediaMaker\Units;

use AllowDynamicProperties;
use Arris\Entity\Result;

#[AllowDynamicProperties]
class FFProbe
{
    public Result $state;

    public $raw;
    public $streams;
    public $format;

    public function __construct($filename)
    {
        $this->state = new Result();

        if (!is_readable($filename)) {
            $this->state->error("File not readable");
            return false;
        }

        $this->mime_type = mime_content_type($filename);
        if ($this->mime_type === false) {
            $this->state->error("Can't get MIMETYPE");
            return false;
        }

        $json = shell_exec("ffprobe -v quiet -print_format json -show_format -show_streams {$filename} 2>&1");
        $this->raw = json_decode($json);

        if (empty($this->raw)) {
            $this->state->error("File not supported by FFMPEG: not a video, audio or photo");
            return false;
        }

        if (empty($this->raw->streams)) {
            $this->state->error("No streams found in file");
            return false;
        }
        $this->streams = $this->raw->streams;
        $this->format = $this->raw->format;

        // ищем поток данных с кодеком типа video
        $visual = array_find($this->streams, function ($s) {
            return $s->codec_type == "video";
        });

        if (empty($visual)) {
            $this->state->error("Can't find stream with codec type 'video': this is not a video or photo file");
            return false;
        }

        $this->width = $visual->coded_width ?? $visual->width;
        $this->height = $visual->coded_height ?? $visual->height;
        $this->duration = $visual->duration;


    }

    /**
     * apt install magic
     *
     * @param $filename
     * @return string
     */
    function detectFileMimeType($filename='')
    {
        $filename = escapeshellcmd($filename);
        // $command = "file -b --mime-type -m /usr/share/misc/magic {$filename}";
        $command = "file -b --mime-type {$filename}";

        $mimeType = shell_exec($command);

        return trim($mimeType);
    }

}