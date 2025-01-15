<?php

namespace AJURMediaMaker\Units;

use Arris\AppLogger;
use Arris\Entity\Result;

class Convertor
{
    /**
     * @param string $filename
     * @param string $mimetype
     * @param array $processing_properties
     *
     * @return Result
     */
    public static function convert(string $filename, string $mimetype = '', array $processing_properties = []):Result
    {
        $from = config('path.storage.inbound') . DIRECTORY_SEPARATOR . $filename;
        $to = config('path.storage.outbound') . DIRECTORY_SEPARATOR . $filename;
        $assets = config('path.assets');

        $is_image = str_contains($mimetype, 'image/');
        $is_video = str_contains($mimetype, 'video/');

        $probe = new FFProbe($from);

        $watermark_file = match ($processing_properties['watermark']) {
            "30"        =>  'watermark_30%.png',
            "50"        =>  'watermark_50%.png',
            "100"       =>  'watermark_100%.png',
            "200"       =>  'watermark_200%.png',
            default     =>  'watermark_none.png'
        };

        $logo_file = match ($processing_properties['site']) {
            '47news'    =>  'logo_47news.png',
            default     =>  'logo_fontanka.png'
        };

        // для 47news не используем вотермарку
        if ($processing_properties['site'] === '47news') {
            $watermark_file = 'watermark_none.png';
        }

        $logo_corner = match ($processing_properties['corner']) {
            'NW'        =>  'overlay=10:10',
            'NE'        =>  'overlay=W-w-10:10',
            'SW'        =>  'overlay=W-w-10:H-h-10',
            'SE'        =>  'overlay=10:H-h-10',
            default     =>  'overlay=w/2;h/2'
        };

        // вот тут будем настраивать scale логотипа в зависимости от...
        $logo_scale_w = "iw*1";

        $filter_complex = [
            '[1:v]loop=loop=8:size=1:start=0,tile=3x3[wmtiled];',
            '[0:v][wmtiled]overlay=0:0[wm];',
            "[2:v]scale={$logo_scale_w}:-1[logo];",
            "[wm][logo]{$logo_corner}"
        ];

        // формируем команду ffmpeg
        $ffmpeg_command = [
            "ffmpeg",
            "-y",
            "-i {$from}",
            "-i {$assets}/{$watermark_file}",
            "-i {$assets}/{$logo_file}",
            '-filter_complex ',
            '"',
            implode(' ', $filter_complex),
            '"',
            '-q:v 0',
            $to
        ];

        $command = implode(' ', $ffmpeg_command);

        AppLogger::scope('convert')->debug($command);

        $output = [];
        exec($command, $output, $result_code);

        $r = new Result();
        $r->setData($output);
        $r->setCode($result_code);

        return $r;
        // return copy($from, $to);
    }

}