run:
	cp .env.example .env
	docker compose up -d --build --force-recreate
	docker exec -t smallit_php bash -c "composer install;php artisan key:generate; php artisan migrate;"
