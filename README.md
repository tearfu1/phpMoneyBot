# phpMoneyBot
Я испльзовал ngrok для настройки вебхука
Запускал через xampp
- sudo cp -r /current_dir/* /opt/lampp/htdocs
- sudo /opt/lampp/xampp start
- ngrok http 80
- curl -F "url=ngrok_link" https://api.telegram.org/bot6964808672:AAHnFx2pttYCV7grTjNcqvhv32EmtI4D1f0/setWebhook
- Не забыть в конце удалить вебхук 
- curl -F "url=ngrok_link" https://api.telegram.org/bot6964808672:AAHnFx2pttYCV7grTjNcqvhv32EmtI4D1f0/deleteWebhook
