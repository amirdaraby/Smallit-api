run:
	cp .env.example .env
	docker compose up -d --build --force-recreate
	chgrp -R www-data storage bootstrap/cache
	chmod -R ug+rwx storage bootstrap/cache
	docker exec -t smallit_php bash -c "composer install;php artisan key:generate --force; php artisan migrate --force;php artisan horizon:install"