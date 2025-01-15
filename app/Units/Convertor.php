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

        // $probe = new FFProbe($from);

        // на самом деле конвертируем видео всегда, потому что галочка с фронта не приходит, а приходит NULL
        $is_convert = match (@$processing_properties['convert']) {
            'on'    =>  true,
            default =>  false
        };

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

        // $use_cpu_cores = _env('CRON.MEDIA_CONVERT.MAX_CPU_CORES', 'all', 'string');

        // формируем команду ffmpeg
        $ffmpeg_command = [
            // 'limitCPU'  =>  $use_cpu_cores != 'all' ? "taskset -c {$use_cpu_cores}" : '',
            "ffmpeg",
            "-y",
            "-i {$from}",
            "-i {$assets}/{$watermark_file}",
            "-i {$assets}/{$logo_file}",
            '-filter_complex ',
            '"',
            implode(' ', $filter_complex),
            '"',
        ];

        if ($is_image) {
            $ffmpeg_command = array_merge($ffmpeg_command, [
                '-q:v 0',
            ]);
        } else {
            // $probe = new FFProbe($from);
            // $params = self::determineVideoParams($probe);

            $ffmpeg_command = array_merge($ffmpeg_command, [
                // 'scale'     =>  "-vf scale={$params['vf_scale']}", //@todo: нужно убрать внутрь filter_comples
                'vsync'     =>  "-vsync 1",
                'framerate' =>  "-r 24",
                "vcodec"    =>  "-c:v libx264",
                'vbitrate'  =>  '-b:v 2M',        //@todo: no more than given, see $probe->?
                'x264params'=>  '-x264-params "keyint=60:min-keyint=24:vbv-maxrate=2000:vbv-bufsize=4000"',
                'moveflags' =>  '-movflags +faststart',
                'acodec'    =>  "-c:a aac -b:a 128k",
                'metadata'  =>  '-metadata copyright="AJUR Media Maker bot"'

            ]);
        }

        // target
        $ffmpeg_command = array_merge($ffmpeg_command, [ $to ]);

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

    private static function determineVideoParams(FFProbe $probe):array
    {
        if ($probe->height > $probe->width) {
            $vf_scale = '720:-2';
            $target_width = 720;
            $target_height = round(720 * $probe->height / $probe->width);
        } else {
            $vf_scale = '-2:720';
            $target_height = 720;
            $target_width = round(720 * $probe->width / $probe->height);
        }

        return [
            'is_even'   =>  true,
            'width'     =>  $target_width,
            'height'    =>  $target_height,
            'vf_scale'  =>  $vf_scale
        ];
    }

}