up:
	./vendor/bin/sail stop
	./vendor/bin/sail up -d
	./vendor/bin/sail composer install
	./vendor/bin/sail artisan migrate --seed --force
	nohup sh -c "./vendor/bin/sail artisan mqtt-to-db:run && ./vendor/bin/sail artisan schedule:work" > /dev/null &

down:
	./vendor/bin/sail stop

fake-msg:
	./vendor/bin/sail artisan mqtt:send-fake

tinker:
	@echo "For immediately raw MQTT message parsing write:"
	@echo "(new IoTMessageTransporter)->run();"
	./vendor/bin/sail artisan tinker
