<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Конвертация фото или видео</title>
    <script src="/assets/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
        }
        button {
            padding: 10px;
            border: 1px solid black;
            border-radius: 5px;
            background-color: white;
            color: black;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .button-active {
            color: red; /* Цвет текста для активной кнопки */
        }
        button:hover {
            background-color: blanchedalmond;
        }
        .checkbox-group {
            margin: 10px 0;
        }
    </style>
    <script>
        _ = function (selector) {
            if (typeof selector === 'string') {
                const elements = document.querySelectorAll(selector);
                return elements.length === 1 ? elements[0] : elements;
            } else if (selector instanceof Element) {
                return selector;
            } else {
                throw new SyntaxError("Neither a string nor a DOM-element");
            }
        }

        // Объект для хранения состояния фильтров
        let properties = { };

        // все кнопки по-умолчанию
        let buttons = { };

        document.addEventListener("DOMContentLoaded", function() {
            window.onload = initButtonStates;

            buttons = document.querySelectorAll('.action-select-option');

            buttons.forEach(button => {
                button.addEventListener('click', handleButtonClick);
            });

            _(`button[type="reset"]`).addEventListener('click', resetHandler);
            _(`#form`).addEventListener('submit', function (event) {
                event.preventDefault();

                if (!properties.site) {
                    alert('Не выбран раздел Фонтанка / 47news');
                    return false;
                }

                if (!properties.watermark) {
                    alert('Не выбран тип вотермарки');
                    return false;
                }

                if (!properties.corner) {
                    alert('Не выбран угол размещения логотипа');
                    return false;
                }

                if (!_(`#income_file`).files.length) {
                    alert('Пожалуйста, выберите файл для загрузки.');
                    return false;
                }

                const resultBlock = document.getElementById('result');
                resultBlock.style.display = 'block'; // Делаем блок видимым
                resultBlock.innerHTML = 'Конвертируем...'; // Устанавливаем текст "Конвертируем"

                let formData = new FormData(this);
                formData.append('properties', JSON.stringify(properties));

                // Отправляем данные на сервер
                fetch(this.getAttribute('action'), {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.text()) // Получаем ответ в текстовом формате
                    .then(data => {
                        resultBlock.innerHTML = data; // Записываем ответ сервера в блок
                    })
                    .catch(error => {
                        console.error('Ошибка:', error);
                        resultBlock.innerHTML = 'Произошла ошибка при конвертации.';
                    });

            });
        });

        /**
         * Инициализация состояния при загрузке страницы на основе locationHash
         */
        function initButtonStates() {
            const hash = window.location.hash.substring(1); // remove #
            const params = new URLSearchParams(hash);

            properties.site = params.get('site');
            properties.watermark = params.get('watermark');
            properties.corner = params.get('corner');

            for (const [key, value] of Object.entries(properties)) {
                if (key && value) {
                    const button = document.querySelector(`button[data-name="${ key }"][data-value="${ value }"]`);
                    if (button) {
                        button.classList.add('button-active');
                    }
                }
            }

        }

        /**
         * Кнопка reset
         */
        function resetHandler() {
            properties = { };
            window.location.hash = '';
            history.replaceState(null, null, window.location.pathname + window.location.search);
        }

        /**
         * Клик по кнопке
         * @param event
         */
        function handleButtonClick(event) {
            const button = event.target;
            const buttonName = button.getAttribute('data-name');
            const buttonValue = button.getAttribute('data-value');

            // Изменяем цвет текста на активной кнопке
            buttons.forEach(btn => {
                if (btn === this) {
                    btn.classList.add('button-active');
                } else {
                    if (btn.getAttribute('data-name') == buttonName) {
                        btn.classList.remove('button-active');
                    }
                }
            });

            // Обновляем объект состояния
            properties[buttonName] = buttonValue;

            // Обновляем window.location.hash
            const hashString = Object.entries(properties)
                .filter(([key, value]) => value !== null)
                .map(([key, value]) => `${ key }=${ value }`)
                .join('&');
            window.location.hash = hashString;
        }

    </script>
</head>
<body>

<div class="container">
    <h1>Обработка фото или видео</h1>
    <form action="upload.php" method="post" enctype="multipart/form-data" id="form">
        <input type="hidden" name="MAX_FILE_SIZE" value="{$max_upload_size}" />
        <div class="form-group">
            <input type="file" name="income_file" id="income_file">
        </div>
        <hr>
        <div class="form-group">
            <label>Тип накладываемого логотипа:</label>
            <div class="button-group">
                <button class="action-select-option" type="button" data-name="site" data-value="fontanka">Фонтанка</button>
                <button class="action-select-option" type="button" data-name="site" data-value="47news">47news</button>
            </div>
        </div>

        <div class="form-group">
            <label>Watermark (фоновое изображение):</label>
            <div class="button-group">
                <button class="action-select-option" type="button" data-name="watermark" data-value="0">без вотермарки </button>
                <button class="action-select-option" type="button" data-name="watermark" data-value="30">30% </button>
                <button class="action-select-option" type="button" data-name="watermark" data-value="50">50%</button>
                <button class="action-select-option" type="button" data-name="watermark" data-value="100">100%</button>
                <button class="action-select-option" type="button" data-name="watermark" data-value="200">200%</button>
            </div>
        </div>

        <div class="form-group">
            <label>Угол логотипа:</label>
            <div class="button-group">
                <button class="action-select-option" type="button" data-name="corner" data-value="NW" style="font-size: x-large">&#8598;</button>
                <button class="action-select-option" type="button" data-name="corner" data-value="NE" style="font-size: x-large">&#8599;</button>
                <button class="action-select-option" type="button" data-name="corner" data-value="SE" style="font-size: x-large">&#8601;</button>
                <button class="action-select-option" type="button" data-name="corner" data-value="SW" style="font-size: x-large">&#8600;</button>
            </div>
        </div>

        <div class="form-group">
            <label>Размер логотипа (рекомендуется 30%):</label>
            <div class="button-group">
                <button class="action-select-option" type="button" data-name="logoscale" data-value="10">10%</button>
                <button class="action-select-option" type="button" data-name="logoscale" data-value="20">20%</button>
                <button class="action-select-option button-active" type="button" data-name="logoscale" data-value="30">30%</button>
                <button class="action-select-option" type="button" data-name="logoscale" data-value="40">40%</button>
                <button class="action-select-option" type="button" data-name="logoscale" data-value="50">50%</button>
            </div>
        </div>

        <div class="checkbox-group">
            <label>
                <input type="checkbox" name="compress_video" checked>Привести формат видео к ...
            </label>
        </div>

        <div class="form-group">
            <div class="button-group">
                <button type="reset">СБРОС</button>
                <button type="submit">НАЧАТЬ</button>
            </div>
        </div>
    </form>
    <hr>
    <div id="result">

    </div>
</div>

</body>
</html>
