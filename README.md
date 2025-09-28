WordPress OG Image Generator Plugin

Custom WordPress plugin for automatic Open Graph image generation using ImageMagick.
Integrates with Rank Math via hooks to provide dynamic OG images for posts, custom post types, and tags.

🚀 Features

Automatic OG image generation for specific post types and tags

Integration with Rank Math via add_action() and add_filter()

Uses ImageMagick for better image quality (initially tested GD, but replaced)

Prevents duplicate overwrites of generated images

Organized structure for background images and fonts

Extendable: easily add new methods for more taxonomies or post types

📂 Project Structure
plugin-folder/
│── fonts/                 # Font files
│── backgrounds/           # Background images
│── og-image-generator.php # Main plugin file (class + hooks)

🛠️ Main Class Overview

__construct() – registers hooks with add_action and add_filter

word_wrap_imagick() – prepares and formats text for OG image

generate_image() – main image generation logic

generate_image_post_tag() – OG images for tags

generate_image_cryptocurrency() – OG images for custom post type cryptocurrency

allow_picture_overwrite() – prevents unnecessary overwrites

check_save_img_path() / generate_save_img_path() – manages image paths

rank_math_opengraph_image() – sets OG image depending on page

check_page_post_tag() / check_page_cryptocurrency() – validate and return images

⚙️ Installation

Copy the plugin folder into wp-content/plugins/

Activate via WordPress Admin → Plugins

Make sure ImageMagick extension is enabled in PHP

📸 Example Workflow

Add a new post or tag → Plugin generates OG image automatically

Rank Math detects the generated image via filters

Facebook/Twitter share preview shows the new OG image

📖 Notes

Originally tested with GD, but due to limitations in text rendering and image quality, switched to ImageMagick

Can be extended for additional post types and taxonomies with minimal code changes

На русском.

Генерация картинок для Open Graph в Wordpress.
Добавление OG картинок через Rank Math для facebook и twitter через хуки.
Добавление сгенерированных картинок для одного типа постов и для тэгов через хуки.

Этапы выполнение задачи:
1 Создание и подключение плагина

2 Подключение php библиотеки Image Magic ( Сначала выбрал GD но из за трудности работы с ним переключился на Image Magic )

3 В плагине были созданы две папки, одна для картинок Бэкграунда а другая для шрифтов.

4 В основном php файле плагина я создал класс который будет отвечать за полный цикл генерации картинок а также за подключение через хуки.

5 По методам в классе:
5.1  __construct() - содержит только подключение по хукам с использованием add_action() - для тегов и таксономии а add_filter() для Rank Math.
5.2 Метод word_wrap_imagick() - проверка и настройка текста который будет отображаться на сгенерированной картинке
5.3 Метод generate_image() - генерация картинки с полными настройками
5.4 Метод generate_image_post_tag() - генерация картинок для Тегов.
5.5 Метод generate_image_cryptocurrency() - генерация картинок для записей из Post type - cryptocurrency.
5.6 Метод allow_picture_overwrite() - этот метод проверяет если генерация картинки уже была то то верни false иначе true.(простая проверка чтоб не было несколько перезаписей)
5.7 Метод check_save_img_path() - возвращает картинку из папки.
5.8 Метод generate_save_img_path() - возвращает картинку из папки.
5.9 Метод rank_math_opengraph_image() - проверяет на какой странице находится пользователь и выводит определённую картинку.
5.10 Метод check_page_post_tag() - проверяет на наличие картинки и выводит результат.
5.11 Метод check_page_cryptocurrency() - проверяет на наличие картинки и выводит результат.

Задача одна из интересных задач. 
Потребовалось изучение библиотек GD и Image Magic. Были трудности с качеством картинки и использование библиотеки Image Magic, но всё смог настроить.
