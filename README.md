# PHP-ConsoleGraph

Библиотека для "рисования" окон в консоли. Учитывая возможности PHP - "окна" получаются линейными, но достаточно приятными. 

Создаются окна размером 120x30 символов, что достаточно для небольших "окошек" в вашем приложении.

Библиотека кросс-платформенна (т.е. поддерживается режим "windows" - с использованием команд терминала и режим "ANSI" - с использованием escape-последовательностей).

Доступно рисование "графических" линий, стенок окон, полей ввода текста и паролей, установка заголовка окна терминала, прогресс-бары (односессионные, однако на PHP - какие есть).

Бонусом идёт конвертер изображений из PNG в снимок окна терминала (текстовый файл из необходимой псевдографики), позволяющий преобразовывать чёрно-белые изображения в текст, для последующего вывода на экран в качестве экрана загрузки, например (принимаются на вход 120x60px чёрно-белые изображения).

Ниже - описание класса и его публичных функций.

### Описание функционала

#### Содержание

##### Список классов:
* ConsoleGraph

##### Список функций:
* ConsoleGraph->graphTitle($title)
* ConsoleGraph->graphColor($bg, $txt)
* ConsoleGraph->graphColorReset()

* ConsoleGraph->graphClear() 
* ConsoleGraph->clear()

* ConsoleGraph->graphSetSlide($slide) 

* ConsoleGraph->graphReadLn($text = null)
* ConsoleGraph->graphReadPassword($text = null)

* ConsoleGraph->graphStartingLine()
* ConsoleGraph->graphEmptyLine()
* ConsoleGraph->graphFilledLine()
* ConsoleGraph->graphDottedLine() 
* ConsoleGraph->graphEndingLine()

* ConsoleGraph->graphWriteToLine($text)
* ConsoleGraph->graphWriteToCenterLine($text)

* ConsoleGraph->graphProgressBarCreate()
* ConsoleGraph->graphProgressBarUpdate($current, $count) 
* ConsoleGraph->graphProgressBarClose()

* ConsoleGraph->graphPause()
* ConsoleGraph->graphFinish()


#### Описание классов

##### ConsoleGraph

Главный и единственный класс, реализующий "графическую систему" библиотеки. Является публичным нестатическим классом, то есть для использования необходимо создать его инстанцию.

Создание инстанции:

```$console = new ConsoleGraph();```

Конструктор класса может принимать несколько типов данных: строковой и булевский (остальные будут так или иначе интерпретированны PHP, возможно - неверно). 

Значения переменных: 
- $useStarsAsWinBuilders - по умолчанию в качестве декорации окна используются псевдографические символы. Также есть возможность использовать звёздочки "\*". False - не изпользовать звёздочки, True - использовать, также существует отладочная опция '\_\_do_not_configure_window' - не конфигурировать окно при инициализации, то есть не изменять размер терминала на необходимые 120x30 символов и не фиксировать прокрутку.


#### Описание функций

##### ConsoleGraph->graphTitle($title)

Устанавливает имя окна терминала.

Значения переменных:

* String $title

##### ConsoleGraph->graphColor($bg, $txt)

Устанавливает цвета фона и текста в терминале (работает только на Windows!)

Значения переменных:

* int $bg - цвет фона (0x0 - 0xF)
* int $txt - цвет текса (0x0 - 0xF)

##### ConsoleGraph->graphColorReset()

Сбрасывает цвета фона и текста в терминале (работает только на Windows!)


##### ConsoleGraph->graphClear()

Очистка экрана терминала 

##### ConsoleGraph->clear()

Очистка экрана терминала (устаревший (deprecated) метод)


##### ConsoleGraph->graphSetSlide($slide) 

Устанавливает сдвиг текста от начала отсчёта, полезно при кастомном позиционировании текста на экране

Значения переменных:

* int $slide - размер сдвига в символах


##### ConsoleGraph->graphReadLn($text = null)

Читает строку с клавиатуры, возвращая её значение. Параметром можно указать текст, указываемый перед индикатором ввода '> '.

Значения переменных:

* string $text - текст перед индикатором ввода '> '.

Тип данных:

* string 

##### ConsoleGraph->graphReadPassword($text = null)

Читает строку с клавиатуры, возвращая её значение, а затем скрывая введённый текст спец. символами, как для пароля. Параметром можно указать текст, указываемый перед индикатором ввода '> '.

Значения переменных:

* string $text - текст перед индикатором ввода '> '.

Тип данных:

* string 


##### ConsoleGraph->graphStartingLine()
##### ConsoleGraph->graphEmptyLine()
##### ConsoleGraph->graphFilledLine()
##### ConsoleGraph->graphDottedLine() 
##### ConsoleGraph->graphEndingLine()

##### ConsoleGraph->graphWriteToLine($text)
##### ConsoleGraph->graphWriteToCenterLine($text)

##### ConsoleGraph->graphProgressBarCreate()
##### ConsoleGraph->graphProgressBarUpdate($current, $count) 
##### ConsoleGraph->graphProgressBarClose()

##### ConsoleGraph->graphPause()
##### ConsoleGraph->graphFinish()



