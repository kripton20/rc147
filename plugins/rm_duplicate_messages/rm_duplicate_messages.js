function msg_search(day1, month1, year1, day2, month2, year2){
	// делаем запрос на сервер - берём массив списка писем

	// включаем блокировку интерфейса, выводим сообщение о работе процедуры поиска дубликатов
	var lock = rcmail.set_busy(true, 'rm_duplicate_messages.checkdpl'),
	// этот параметр для того чтобы это сообщение перекрывалось следующим сообщением
	// о том что процедура поиска дубликатов сообщений завершена
	params = rcmail.check_recent_params();
	// запускаем PHP-функцию поиска дубликатов сообщений - 'select_msg'
	// вызываем метод http_post объекта rcmail (параметры через запятую)
	rcmail.http_post('plugin.select_msg', params, lock);
}
// зупускаем процедуру поиска/удаления дубликатов сообщений
function msg_search_start () {
	// переменные диалогового окна
	//var day1;
	// контент диалогового окна
	var content = '<H3 align="center">'+rcmail.get_label('rm_duplicate_messages.label4')+'</H4><br/>'+
	// формируем таблицу выбора даты
	// аттрибут cellspacing - расстояние между ячейками
	'<table border="0" align="center" cellpadding="0" cellspacing="10">'+
	'<tr align="center"><td></td>'+
	// получаем надписи на локализованном языке из файла локализации
	'<td><strong>'+rcmail.get_label('rm_duplicate_messages.label5')+'</strong></td>'+
	'<td><strong>'+rcmail.get_label('rm_duplicate_messages.label6')+'</strong></td>'+
	'<td><strong>'+rcmail.get_label('rm_duplicate_messages.label7')+'</strong></td></tr>'+
	'<tr align="center"><td><strong>'+rcmail.get_label('rm_duplicate_messages.label8')+'</strong></td>'+
	'<td><input type="number" id="day1" size="5" min="1" max="31" value="1" tabindex="0" style="padding-bottom:5px; padding-top:5px" /></td>'+
	'<td><input type="number" id="month1" size="5" min="1" max="12" value="1" tabindex="0" style="padding-bottom:5px; padding-top:5px" /></td>'+
	'<td><input type="number" id="year1" size="5" min="1990" max="2050" value="1996" tabindex="0" style="padding-bottom:5px; padding-top:5px" /></td></tr>'+
	'<tr align="center"><td><strong>'+rcmail.get_label('rm_duplicate_messages.label9')+'</strong></td>'+
	'<td><input type="number" id="day2" size="5" min="1" max="31" value="1" tabindex="0" style="padding-bottom:5px; padding-top:5px" /></td>'+
	'<td><input type="number" id="month2" size="5" min="1" max="12" value="1" tabindex="0" style="padding-bottom:5px; padding-top:5px" /></td>'+
	'<td><input type="number" id="year2" size="5" min="1990" max="2050" value="1996" tabindex="0" style="padding-bottom:5px; padding-top:5px" /></td>'+
	'</tr></table>';
	// заголовок диалогового окна
	var title = rcmail.get_label('rm_duplicate_messages.label3');
	// кнопки диалогового окна
	buttons={
	};
	// кнопка запуска процедуры - 'Отметить дубликаты'
	buttons[rcmail.get_label('rm_duplicate_messages.label10')] = function(e) {
		// получаем значения полей формы
		var day1 = document.getElementById('day1').value,
		month1 = document.getElementById('month1').value,
		year1 = document.getElementById('year1').value,
		day2 = document.getElementById('day2').value,
		month2 = document.getElementById('month2').value,
		year2 = document.getElementById('year2').value;
		// отключим нашу коммандную кнопку
		window.rcmail.enable_command('plugin.btn_cmd_rm_dublecates', false);
		// закрываем окно
		$(this).remove();
		// вызываем функцию 'msg_search'
		msg_search(day1, month1, year1, day2, month2, year2, buttons);
	};
	// кнопка запуска процедуры - 'Удалить дубликаты'
	buttons[rcmail.get_label('rm_duplicate_messages.label11')] = function(e) {
		// закрываем окно
		$(this).remove();
	};
	// кнопка отмены
	buttons[rcmail.get_label('cancel')] = function(e) {
		// закрываем окно
		$(this).remove();
	};
	// показываем диалоговое окно
	rcmail.show_popup_dialog(content, title, buttons);
}

// функция перебора массива 'lst_msg' полученного от функции 'lst_msg' из скрипта PHP
function lst_msg(lst_msg, folder){
	// присвоим переменной 'lst_msg' и 'folder' значение массива 'lst_msg' и имя папки 'folder' из PHP-скрипта
	var lst_msg=rcmail.env.lst_msg, folder=rcmail.env.folder;
	
	
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
			rcmail.register_command('plugin.btn_cmd_rm_dublecates', msg_search_start, true);
		});

	// слушает событие от функции 'lst_msg'
	rcmail.addEventListener('plugin.lst_msg', function (a,b){
			// вызываем функцию 'lst_msg' с двумя параметрами a и b (массив писем и текущая папка)
			lst_msg(a,b);
		});

	// функция включения кнопки и сообщения о завершении работы процедуры по поиску дубликатов
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
			// выводим уведомление о завершении работы нашей процедуры - удаления дубликатов сообщений
			// в первом параметре получаем локализованную метку, во втором указываем тип выводимого сообщения
			rcmail.display_message(msg, 'confirmation');
			// обновим страницу
			rcmail.refresh();
		});
}

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
