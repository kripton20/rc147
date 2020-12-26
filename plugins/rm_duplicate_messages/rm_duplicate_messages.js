// зупускаем процедуру поиска дубликатов сообщений
function msg_search(){
	// получим значение 'uids' выделенного элемента в списке элементов
	var uids = rcmail.message_list.get_selection();
	// остановим работу функции и выведем сообщение если значение 'uids' не получено
	if (!uids) return window.alert('\n' + "Неудаётся получить uid сообщения." + '\n' + '\n' + "Перезагрузите страницу.");
	// включаем блокировку интерфейса: выводим сообщение о работе процедуры поиска дубликатов
	// параметр 'lock' для того чтобы это сообщение перекрывалось следующим сообщением
	// о том что процедура поиска дубликатов сообщений завершена
	var lock = rcmail.set_busy(true, 'rm_duplicate_messages.checkdpl');
	// запускаем PHP-функцию поиска дубликатов сообщений 'rm_dublecates':
	// вызываем метод 'http_post' объекта 'rcmail' (параметры через запятую)
	// метод 'selection_post_data()' отправляет данные на сервер в массив [_POST]
	rcmail.http_post('plugin.rm_dublecates', rcmail.selection_post_data({_uid: uids}), lock);
	// отключим нашу коммандную кнопку
	window.rcmail.enable_command('plugin.btn_cmd_rm_dublecates', true);
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
// инициализируем объект 'rcmail: rcube_webmail'
$(document).ready(function() {
		// если инициализирован объект 'window.rcmail' выполняем операторы в условии
		if (window.rcmail) {
			/**
			* Добавление и регистрация слушателей событий
			* Это делается с помощью следующих двух функций:
			* 	rcmail.addEventListener|removeEventListener('event', callback);
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
			// параметр 'init' - событие, параметр 'function(evt)' это - ананимная callback-функция
			rcmail.addEventListener('init', function(evt) {
					/**
					* Пользовательские команды должны быть зарегистрированы вместе с функцией обратного вызова,
					* которая должна выполняться, если команда запускается.
					* Третий аргумент активирует команду сразу после регистрации.
					* Пример: rcmail.register_command(команда, функция_обработчик, включить)
					* Примечание: одна команда - один обработчик
					*/
					// регистрация комманды которую выполняет кнопка (объявление функции наверху)
					//rcmail.register_command('plugin.btn_cmd_rm_dublecates', msg_search, true, rcmail.env.uid);
					rcmail.register_command('plugin.btn_cmd_rm_dublecates', msg_search, true);
					// условие срабатывает если сформировался список сообщеений
					if (rcmail.message_list) {
						// просдушиватель событий срабатывает если сообщеение выделено
						rcmail.message_list.addEventListener('select', function(list) {
								// включаем командную кнопку если выделено больше одного сообщения в списке
								rcmail.enable_command('plugin.btn_cmd_rm_dublecates', list.get_selection(false).length > 1);
							});
					}
				});
			// функция уведомления об окончании проверки на дубликаты и включения кнопки
			rcmail.addEventListener('plugin.successful', function (){
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
	});
