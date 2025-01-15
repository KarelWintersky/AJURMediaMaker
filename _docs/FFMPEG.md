```shell

ffmpeg -y -i input.jpg -i watermark.png -i logo.png -filter_complex "[0:v][1:v] overlay=0:0 [wm]; [wm][2:v] overlay=x:y" -q:v 0 output.jpg

# NW
ffmpeg -y -i input.jpg -i watermark.png -i logo.png -filter_complex "[0:v][1:v] overlay=0:0 [wm]; [wm][2:v] overlay=10:10" -q:v 0 output.jpg

# NE
ffmpeg -i input.jpg -i watermark.png -i logo.png -filter_complex "[0:v][1:v] overlay=0:0 [wm]; [wm][2:v] overlay=W-w-10:10" -q:v 0 output.jpg

# SW
ffmpeg -i input.jpg -i watermark.png -i logo.png -filter_complex "[0:v][1:v] overlay=0:0 [wm]; [wm][2:v] overlay=10:H-h-10" -q:v 0 output.jpg

# SE
ffmpeg -i input.jpg -i watermark.png -i logo.png -filter_complex "[0:v][1:v] overlay=0:0 [wm]; [wm][2:v] overlay=W-w-10:H-h-10" -q:v 0 output.jpg
```

# Финальный вариант:

С вотермаркой и лого:

```shell
ffmpeg -y -i input_xl.jpg -i watermark.png -i logo.png -filter_complex "[1:v]loop=loop=8:size=1:start=0,tile=3x3[wmtiled];[0:v][wmtiled]overlay=0:0[wm];[2:v]scale=iw:-1[logo];[wm][logo]overlay=10:10 " -q:v 0 output.jpg
```

Угол логотипа:
- NW = `overlay=10:10`
- NE = `overlay=w-10:10`
- SW = `overlay=10:h-10`
- SE = `overlay=w-10:h-10`

Scale логотипа:
- `[2:v]scale=iw/2:-1[logo]` - логотип пополам
- `[2:v]scale=iw:-1[logo]` - логотип целиком, можно указать и iw/2 для маленьких картинок и iw*2 для больших

А теперь без вотермарки:

```shell
ffmpeg -y -i input_xl.jpg -i 1x1.gif -i logo.png -filter_complex "[1:v]loop=loop=8:size=1:start=0,tile=3x3[wmtiled];[0:v][wmtiled]overlay=0:0[wm];[2:v]scale=iw:-1[logo];[wm][logo]overlay=10:10 " -q:v 0 output.jpg
```
