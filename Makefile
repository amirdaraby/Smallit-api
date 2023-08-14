run:
	if [! -f $.env]; then \
	cp .env .env.example; \
	fi
	docker compose up -d --build --force-recreate
	chgrp -R www-data storage bootstrap/cache
	chmod -R ug+rwx storage bootstrap/cache
	docker exec -t smallit_php bash -c "composer install -q --no-interaction --no-scripts --prefer-dist;php artisan key:generate --force; php artisan migrate --force;php artisan horizon:install;php artisan test"