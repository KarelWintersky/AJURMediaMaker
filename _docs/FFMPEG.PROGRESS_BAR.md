but another option for a command-line ffmpeg wrapper with a progress bar is the python package ffpb.

You install it with pip (pip install ffpb or pip3 install ffpb).

After that, you'll have a command called ffpb that uses the exact same syntax as ffmpeg itself.

Here's some output from it being used on a movie with burning subtitles.
```
ffpb -i filename.mp4 -vf subtitles=eng.srt:force_style='FontName=DyslexicLo gicFont' -c:a copy
outfile.mp4
filename.mp4: 45%|██████████████▎ | 97752/219384 [30:33<35:29, 57.12 frames/s]
```
But apparently, it doesn't work when you set the log level low or use "--ss/--to/-t" arguments. 

https://github.com/pruperting/code_snippets/blob/master/ffmpeg_progress.sh

https://stackoverflow.com/questions/747982/can-ffmpeg-show-a-progress-bar

https://github.com/sidneys/ffmpeg-progressbar-cli
