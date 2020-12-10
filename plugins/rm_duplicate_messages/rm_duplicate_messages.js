/**
** Клиентские скрипты и элементы пользовательского интерфейса
* Клиентский сценарий может фактически использовать все методы объекта приложения Roundcube.
* Этот объект приложения, к которому можно получить доступ, window.rcmail также предоставляет перехватчики, но немного другим способом:
*  подобно DOM браузера, можно регистрировать прослушиватели событий, используя следующие методы:
*     rcmail.addEventListener('event', callback);
*     rcmail.removeEventListener('event', callback);
*
* Самое главное событие конечно есть init. Здесь плагин может добавлять кнопки и регистрировать свои собственные команды в основном сценарии приложения.
* Вот пример, показывающий, как может выглядеть скрипт плагина:
*    rcmail.addEventListener('init', function(evt) {
*       // создаем пользовательскую кнопку
*       var button = $('<A>').attr('id', 'rcmSampleButton').html(rcmail.gettext('buttontitle', 'sampleplugin'));
*       button.bind('click', function(e){ return rcmail.command('plugin.samplecmd', this); });
*       // добавляем кнопку в контейнер панели инструментов и регистрируем
*       rcmail.add_element(button, 'toolbar');
*       // сообщим приложению, где находится кнопка и за какую команду она отвечает
*       rcmail.register_button('plugin.samplecmd', 'rcmSampleButton', 'link');
*       // свяжем пользовательскую команду (запускаемую обработчиком onclick кнопки) с функцией обратного вызова скрипта клиента плагина
*       rcmail.register_command('plugin.samplecmd', sample_handler, true);
*     });
* // Документация по JavaScript API здесь: https://github.com/roundcube/roundcubemail/wiki/Javascript-API
// https://github.com/roundcube/roundcubemail/wiki/Javascript-API
*/
function button_function () {
	//
	buttons={
	};
	// кнопка запуска процедуры проверки
	buttons[rcmail.get_label('rm_duplicate_messages.label5')] = function(e) {
		// включаем блокировку интерфейса, выводим сообщение о работе процедуры поиска дубликатов
		///var lock = rcmail.set_busy(true, 'rm_duplicate_messages.checkdpl'),
		// этот параметр для того чтобы это сообщение перекрывалось следующим сообщением
		// о том что процедура поиска дубликатов сообщений завершена
		///params = rcmail.check_recent_params();
		// отключим нашу коммандную кнопку
		window.rcmail.enable_command('plugin.btn_cmd_rm_dublecates', false);
		// запускаем PHP-функцию поиска дубликатов сообщений - rm_dublecates
		// вызываем метод http_post объекта rcmail (параметры через запятую)
		///rcmail.http_post('plugin.rm_dublecates', params, lock);

		/*//		Повторная обработка данных ответа на клиенте:
		//    function some_callback_function(response)
		//    {
		//      $('#somecontainer').html(response.message);
		//    }
		//Обратите внимание, что функция обратного вызова может принимать только один аргу-мент. Поэтому вам необходимо упаковать все данные ответа в массив, который будет автоматически преобразован в объект javascript.
		*/
		rcmail.http_post('plugin.rm_dublecates',
			function() {
				$('#dstus').addClass('busy').html(rcmail.get_label('rm_duplicate_messages.checkdpl'));
			}
		);
		// кнопка отмены
		buttons[rcmail.get_label('cancel')] = function(e) {
			$(this).remove();
		};
	};
	// показываем диалоговое окно
	rcmail.show_popup_dialog(rcmail.get_label('rm_duplicate_messages.label4'), rcmail.get_label('rm_duplicate_messages.label3'), buttons);
}


/**
* Запросы и обратные вызовы Ajax
* Если ваши настраиваемые действия на стороне сервера должны отправить обратно некоторые данные
* и запускать настраиваемую функцию обратного вызова на клиенте, клиентские сценарии могут зарегистрировать событие и отправить команду
* с магическим префиксом «плагин». Соответствующее событие на клиенте будет запущено.
* Вот пример того, как это могло бы выглядеть:
* Отправьте ajax-запрос от клиента:
*     rcmail.addEventListener('plugin.somecallback', some_callback_function);
*     rcmail.http_post('plugin.someaction', ...);
*/
if (window.rcmail) {
	/**
	* Добавление регистрация слушателей событий
	* Это делается с помощью следующих двух функций:
	* 	rcmail.addEventListener('event', callback);
	* 	rcmail.removeEventListener('event', callback);
	* Функция callback получает объект события в качестве одного аргумента.
	* Этот объект события содержит свойства, специфичные для события.
	* Они перечислены в качестве аргументов под соответствующим описанием события.
	*
	* EventTargetМетод addEventListener()устанавливает функцию, которая будет вызываться всякий раз,
	* когда будет происходить указанное событие. Общими целями являются Element, Document, и Window,
	* но целью может быть любой объект, поддерживающий события (например XMLHttpRequest).
	*  	addEventListener() работает путем добавления функции или объекта, реализующего EventListener
	* в список прослушивателей событий для указанного типа события на том EventTarget, на котором оно вызывается.
	* https://developer.mozilla.org/en-US/docs/Web/API/EventTarget/addEventListener
	*/
	// регистрация комманды которую выполняет кнопка (объявление функции наверху)
	// параметр 'init' - событие, параметр 'function(evt)' это - callback-функция
	rcmail.addEventListener('init', function(evt) {
			/**
			* Пользовательские команды должны быть зарегистрированы вместе с функцией обратного вызова,
			* которая должна выполняться, если команда запускается.
			* Третий аргумент активирует команду сразу после регистрации.
			* Пример: rcmail.register_command(команда, функция_обработчик, включить)
			* Примечание: одна команда - один обработчик
			*/
			rcmail.register_command('plugin.btn_cmd_rm_dublecates', button_function, true);
		});
	// функция включения кнопки
	rcmail.addEventListener('plugin.successful', function successful(){
			// включим нашу коммандную кнопку
			window.rcmail.enable_command('plugin.btn_cmd_rm_dublecates', true);

			// получим значение переменной от сервера
			// поместим в переменную msg_marked колличество отмеченных сообщений
			var msg_marked = rcmail.env.msg_marked,
			// получим локализованные метки
			msg_successful = rcmail.get_label('rm_duplicate_messages.successful'),
			// в переменную msg поместим полное сообщение которое нужно вывести
			msg = msg_successful + msg_marked;

			// выводим уведомление о завершении работы нашей функции - rm_dublecates обработки сообщений
			// в первом параметре получаем локализованную метку, во втором указываем тип выводимого сообщения
			rcmail.display_message(msg, 'confirmation');

			// обновим страницу
			rcmail.refresh();
		});
}
