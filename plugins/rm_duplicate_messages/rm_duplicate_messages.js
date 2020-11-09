// Документация по JavaScript API здесь: https://github.com/roundcube/roundcubemail/wiki/Javascript-API
// https://github.com/roundcube/roundcubemail/wiki/Javascript-API
function button_function () {

	// buttons={};
	// buttons[rcmail.get_label('delete_old.check')] = function(e) {
	//	rcmail.http_post('plugin.delallold', {'ck':1}, function() {$('#dstus').addClass('busy').html(rcmail.get_label('delete_old.checking')); });};
	// buttons[rcmail.get_label('delete_old.delete')] = function(e) {
	//	rcmail.http_post('plugin.delallold', {}, function() { $('#dstus').addClass('busy').html(rcmail.get_label('delete_old.deleting')); });};
	// buttons[rcmail.get_label('cancel')] = function(e) {$(this).remove();};
	// rcmail.show_popup_dialog(rcmail.get_label('delete_old.dlgtxt'), rcmail.get_label('delete_old.deloldmsgs'), buttons);
	// window.alert('Привет');

	// Отправим ajax-запрос от клиента
	// rcmail.http_post('plugin.$action', ...);
	// следующая строка запускает функцию зарегистрированную вот этой акции:
	//    $this->register_action('plugin.rm_duplicate_messages', array($this,'my_function'));
	// plugin.rm_action_delete_message' - имя акции в массиве акций

	// запускаем метод http_post() объекта rcmail
	/*rcmail.http_post('plugin.activ_folders');

	function activ_folders (){
	rcmail.http_post('plugin.activ_folders');
	}

	function my_function(){
	rcmail.http_post('plugin.my_function');
	}*/
	//var a=rcmail.set_busy(true,"loading");
	rcmail.http_post(
		'plugin.functions_start',
		//a
		// выводим уведомление о работе нашей функции - functions_start обработки сообщений
		//rcmail.display_message('checkdpl','loading')
		// отключим нашу коммандную кнопку
		//window.rcmail.enable_command('plugin.button_comand', false);
	);
}

//function some_callback_function (response)
//{
//	window.alert("Контрольная точка");
//	$('#dstus').html(response.message);
//}

//function enable_command (){
//	// включим комманду нашей кнопки
//	window.rcmail.enable_command('plugin.button_comand', true);
//	window.alert("включим комманду нашей кнопки");
//}

/**
* Повторная обработка данных ответа на клиенте:
* function some_callback_function(response)
* {
*     $('#somecontainer').html(response.message);
* }
* Обратите внимание, что функция обратного вызова может принимать только один аргумент.
* Поэтому вам необходимо упаковать все данные ответа в массив, который будет автоматически преобразован в объект javascript.
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
*     rcmail.addEventListener('init', function(evt) {
*
*       // создаем пользовательскую кнопку
*       var button = $('<A>').attr('id', 'rcmSampleButton').html(rcmail.gettext('buttontitle', 'sampleplugin'));
*       button.bind('click', function(e){ return rcmail.command('plugin.samplecmd', this); });
*
*       // добавляем кнопку в контейнер панели инструментов и регистрируем
*       rcmail.add_element(button, 'toolbar');
*
*       // сообщим приложению, где находится кнопка и за какую команду она отвечает
*       rcmail.register_button('plugin.samplecmd', 'rcmSampleButton', 'link');
*
*       // свяжем пользовательскую команду (запускаемую обработчиком onclick кнопки) с функцией обратного вызова скрипта клиента плагина
*       rcmail.register_command('plugin.samplecmd', sample_handler, true);
*     });
*
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
	/* Добавление регистрация слушателей событий
	Это делается с помощью следующих двух функций:
	rcmail.addEventListener('event', callback);
	rcmail.removeEventListener('event', callback);
	Функция callback получает объект события в качестве одного аргумента.
	Этот объект события содержит свойства, специфичные для события.
	Они перечислены в качестве аргументов под соответствующим описанием события.

	// регистрация комманды которую выполняет кнопка (объявление функции наверху)
	// 'init' - событие, следующая функция это - callback*/
	/**
	EventTargetМетод addEventListener()устанавливает функцию, которая будет вызываться всякий раз,
	когда будет происходить указанное событие. Общими целями являются Element, Document, и Window,
	но целью может быть любой объект, поддерживающий события (например XMLHttpRequest).
	addEventListener() работает путем добавления функции или объекта, реализующего EventListener
	в список прослушивателей событий для указанного типа события на том EventTarget, на котором оно
	вызывается.
	https://developer.mozilla.org/en-US/docs/Web/API/EventTarget/addEventListener
	*/
	rcmail.addEventListener('init', function(evt) {
			/*Пользовательские команды должны быть зарегистрированы вместе с функцией обратного вызова,
			которая должна выполняться, если команда запускается.
			Третий аргумент активирует команду сразу после регистрации.
			Пример: rcmail.register_command(команда, функция_обработчик, включить)*/
			// одна команда - один обработчик
			rcmail.register_command('plugin.button_comand', button_function, true);
		});
	//window.addEventListener('load', function(){window.alert("load");});
	window.addEventListener('test', function(){window.alert("test");});
	window.addEventListener('test', function(){window.alert("test window");});
	document.addEventListener('test', function(){window.alert("test document");});
	window.rcmail.addEventListener('test', function(){window.alert("test window.rcmail");});
	rcmail.addEventListener('test', function(e){window.alert("test rcmail");});
	rcmail.addEventListener('plugin.somecallback1', function some_callback_function1(response1){
			window.alert("plugin.somecallback1");
		});
	rcmail.addEventListener('plugin.somecallback2', function some_callback_function2(response2){
			window.alert("plugin.somecallback2");
		});

	//		rcmail.addEventListener('functions_start', function(evt1){
	//			rcmail.register_command('plugin.somecallback', some_callback_function, true);
	//		});
	//rcmail.addEventListener('plugin.docallback', function (data) {
	// включим комманду нашей кнопки
	//window.alert("Контрольная точка");
	//window.rcmail.enable_command('plugin.button_comand', true);
	//$('#dstus').html(data.msg)
	//$('#dstus').html("ssssssssss");
	//rcmail.refresh();
	//});

	// https://github.com/roundcube/roundcubemail/wiki/Javascript-API
	// https://github.com/roundcube/roundcubemail/wiki/Plugin-API#client-scripts-and-ui-elements
	//rcmail.addEventListener('functions_start', function(evt) {

	/*Пользовательские команды должны быть зарегистрированы вместе с функцией обратного вызова,
	которая должна выполняться, если команда запускается.
	Третий аргумент активирует команду сразу после регистрации.
	Пример: rcmail.register_command(команда, функция_обработчик, включить)*/
	// одна команда - один обработчик
	//rcmail.register_command('plugin.do1', button_function2, true);
	//});

	//rcmail.removeEventListener('init', function(evt){
	//	rcmail.disable_command('plugin.button_comand', button_function, false);});

	// обратный вызов для события загрузки приложения
	//rcmail.addEventListener('plugin.do1') {
	//$('#dstus').removeClass('busy').html(data.msg)
	//rcmail.refresh();});
	//	rcmail.disable_command('plugin.button_comand');
	//window.alert('комманда проходит');
	//}
}
