iTunes Movie trailers
=====================================

Запуск
--------------
1) Для запуска docker измените в env и docker-compose.yaml параметры msql (в докерфайле нету запуска сервера)
2) Для локального dev сервера комманда php bin/console server:start
3) Комманды база данных
    - php bin/console orm:schema-tool:create
    - php bin/console orm:schema-tool:update --force
    - php bin/console orm:clear-cache:metadata
4) Парсинг 10 записей из iTunes php bin/console fetch:trailers

Список изменений
--------------
1) Изменено fetch:trailers выводит 10 записей. 
2) Изменена проверка дубликатов RSS детей. 
3) Заменил получение RSS, с request на simplexml_load_file (фича? я так не думаю)
4) Добавлена Авторизация и Регистрация (ах жалко нету make)
5) Добавлен Список с постерами iTunes. Страница описания. 
6) Добавлена страница Понравившееся и кол-во лайков.
7) Изменено WebProvider. Добавлена возможность внести массив методов. Прописал цикл на добавления контроллеров.