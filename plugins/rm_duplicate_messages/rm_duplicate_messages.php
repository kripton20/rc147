<?php

//Расширяем наш класс от класса rcube_plugin
class rm_duplicate_messages extends rcube_plugin
{

	// Объявляем общедоступные переменные
	//	public $my_messages;
	//	public $m;
	//	public $args;
	//	public $a;

	/**
	* Инициализация плагина.
	*/
	function init ()
	{
		//Переменная $this относится к текущему классу и представляет собой неявный объект.
		// rc - свойство этого объекта. Запишем туда системные настройки приложения.
		$this->rc = rcmail::get_instance();

		// переменной $tsk присвоим имя текущей задачи приложения
		$tsk = $this->rc->task;

		// в условии проверяем имя текущей задачи приложения, если 'mail'
		if ($tsk == 'mail') {

			/**
			* *** Регистрация хуков ***
			* Получаем заголовки и содержимое письма (но только при открытии письма)
			* Глобальный хук: message_headers_output
			* Срабатывает при построении таблицы заголовков сообщений для отображения.
			* Если вы хотите добавить больше заголовков, вам нужно получить их с помощью хука storage_init.
			*/
			//$this->add_hook('message_headers_output', array ($this,'headers'));

			/**
			* Получаем только заголовки писем
			* Глобальный хук: messages_list
			* Срабатывает перед отправкой списка сообщений.
			* Вы можете использовать его для установки заголовков сообщений, list_cols/list_flags и
			* других переменных rcube_mail_header. Также ис-пользуйте его для передачи дополнительных флагов,
			* связанных с плагином, в пользователь-ский интерфейс с помощью массива extra_flags.
			*/

			/**
			* Получаем только заголовки писем
			* Глобальный хук: messages_list
			* Срабатывает перед отправкой списка сообщений.
			* Вы можете использовать его для установки заголовков сообщений, list_cols/list_flags и
			* других переменных rcube_mail_header. Также ис-пользуйте его для передачи дополнительных флагов,
			* связанных с плагином, в пользователь-ский интерфейс с помощью массива extra_flags.
			*/
			/**
			* Получаем заголовки и содержимое письма (но только при открытии письма)
			* Глобальный хук: message_headers_output
			* Срабатывает при построении таблицы заголовков сообщений для отображения.
			* Если вы хотите добавить больше заголовков, вам нужно получить их с помощью хука storage_init.
			*/
			//$this->add_hook('message_headers_output', array($this,'my_message_headers_output1'));

			/**
			* Получаем только заголовки писем
			* Глобальный хук: messages_list
			* Срабатывает перед отправкой списка сообщений.
			* Вы можете использовать его для установки заголовков сообщений, list_cols/list_flags и
			* других переменных rcube_mail_header. Также ис-пользуйте его для передачи дополнительных флагов,
			* связанных с плагином, в пользователь-ский интерфейс с помощью массива extra_flags.
			*/
			//$this->add_hook('messages_list', array($this,'messages_list'));

			/**
			* Загружаем сообщение
			* */
			//$this->add_hook('message_read', array($this,'message_read'));

			// Вызываем функцию локализации - add_texts() из родительского класса интерфейса плагинов - rcube_plugin
			// Файл локализации добавляется в общий массив $texts
			// В массиве находятся ярлыки добавляемые клиенту
			// localization - это имя папки, в массиве указываем ключи из массива файла локализации
			// метод add_texts() записываетфайл локализации нашего плагина в общий массив локализации
			$this->add_texts('localization', array('label1','label2'));

			// загружаем файл скина плагина
			$this->includeCSS();

			/**
			* Клиентские скрипты и элементы пользовательского интерфейса
			* Конечно, плагины имеют большее отношение, чем просто отслеживание событий на стороне сервера.
			* API плагина также позволяет расширить пользовательский интерфейс и функциональность клиента.
			* Первый шаг – добавить код JavaScript на определенную страницу/действие.
			* Создайте файл сценария в папке вашего плагина, а затем включите его в init() метод вашего класса плагина с помощью
			* $this->include_script('client.js');
			*/
			// добавим код JavaScript на определенную страницу / действие
			// создайте файл сценария в папке вашего плагина, а затем включите его в init() метод вашего класса плагина с помощью
			$this->include_script('rm_duplicate_messages.js');

			// добавим кнопку в определенный контейнер на страницу
			// параметр: array  $param      Хеш - массив с именованными параметрами (используемый в скинах)
			// параметр: string $container  Имя контейнера, куда нужно добавить кнопки
			$this->add_button(
				array(
					'domain'  => $this->ID,// id = "rcmbtn106"
					'type'=> 'link',// тип кнопки
					'label'=> 'label6',// надпись на кнопке
					'title'=> 'label7',// всплывающая подсказка
					'command'=> 'plugin.button_comand',// комманда onclick
					//'onclick'  => "rcmail.command('menu - open', 'enigmamenu', event.target, event)",
					'width'=> 32,// размеры
					'height'=> 32,
					'class'   => 'button clean',// класс стиля class = "button clean"
					'classact'=> 'button clean',
				),
				// в панель управления на верху (toolbar)
				'toolbar');

			/**
			* Регистрация настраиваемых действий
			* Теперь нужно объединить клиентскую функциональность плагина с действиями на стороне сервера.
			* Клиентский сценарий может отправлять на сервер ajax-запросы GET или POST, используя:
			*     rcmail.http_get('plugin.plugin_name', ...)
			* и   rcmail.http_post('plugin.plugin_name', ...).
			* Чтобы направить эти запросы к нужной функции плагина, в init() методе класса плагина регистрируем настраиваемое действие
			* (например, 'plugin.plugin_name'):
			*     $this->register_action('plugin.plugin_name', array($this, 'my_function'));
			* Теперь HTTP-запросы формы ./?task=mail&_action=plugin.plugin_name будут запускать зарегистрированную функцию обратного вызова.
			* Эта функция отвечает за обработку запроса и отправку действительного ответа обратно клиенту.
			* Вызов
			*     rcmail::get_instance()->output->send('plugin_name')
			* функция выполнит свою работу.
			* Пользовательские действия предназначены не только для обслуживания ajax-запросов, но также могут расширять приложение
			* с помощью пользовательских экранов и шагов.
			*
			* Обработчик для запроса ajax.
			* Зарегистрируем обработчик для определенного действия (запроса) клиента
			* Обратный вызов будет выполнен по запросу типа /?_task=mail&_action=plugin.action
			* @param string $action   Имя действия (_task = mail& _action = plugin.action) (должно быть уникальным)
			* @param mixed  $callback Callback-Функция обратного вызова в виде строки со ссылкой на объект и именем метода:
			* 						  строка с именем глобальной функции (или массивом) обратного вызова ($obj, 'methodname')
			* @param string $owner    Имя плагина, регистрирующего это действие
			* @param string $task     Имя задачи, зарегистрированное этим плагином
			* Пример: $this->register_action('$action', $callback'));
			*         $this->register_action('$action',  array($this,'function'));
			*/
			//$this->register_action('plugin.activ_folders', array($this,'activ_folders'));
			//$this->register_action('plugin.my_function', array($this,'my_function'));
			$this->register_action('plugin.functions_start', array($this,'functions_start'));
		}

		// в условии проверяем имя текущей задачи приложения, если 'settings'
		if ($tsk == 'settings') {

			// загружаем файл скина плагина
			//$this->includeCSS();

			/**
			* Регистрируем хуки сервера
			* Способ работы хуков плагинов заключается в том, что в разное время, пока Roundcube обрабатывает, он проверяет,
			* есть-ли у каких-либо плагинов зарегистрированные функции для запуска в это время, и если да, то функции запускаются
			* (путем выполнения «ловушки»). Эти функции могут изменять или расширять поведение Roundcube по умолчанию.
			* Регистрация хуков:     $this->add_hook('hook_name', $callback_function);
			*   где первый аргумент – это имя раздела куда мы хотим поместить наш хук, хуки содержатся здесь: $this->handlers
			*   где второй аргумент – это обратный вызов PHP (функция в этом файле ниже), который может ссылаться на простую функцию или метод
			* объекта. Зарегистрированная функция получает один хеш-массив в качестве аргумента, который содержит определенные данные текущего
			* контекста в зависимости от ловушки.
			* См. «Перехватчики подключаемых модулей» для получения полного описания всех перехватчиков и их полей аргументов.
			* Аргумент var может быть изменен функцией обратного вызова и может (даже частично) быть возвращен приложению.
			*/
			// Поместим наш хук в секцию preferences_sections_list
			// Позволяет плагину изменять список разделов пользовательских настроек
			// потом поместим наш хук ещё в другую секцию https://github.com / roundcube / roundcubemail / wiki / Plugin - Hooks
			// хуки помещаются в массив $this->$api->$handlers
			### Эти два хука добавляют плагин в секцию.
			//$this->add_hook('preferences_sections_list', array($this,'insert_my_section1'));
			//$this->add_hook('write_log', array($this,'functions_start'));
			//$this->add_hook('preferences_save', array($this,'my_save_settings'));

			// получаем список папок почтового ящика
			//$this->add_hook('render_mailboxlist', array($this,'my_render_mailboxlist1'));

			//$a = 1; // для остановки

			// добавим код JavaScript на определенную страницу / действие
			// создайте файл сценария в папке вашего плагина, а затем включите его в init() метод вашего класса плагина с помощью
			//$this->include_script('rm_duplicate_messages.js');

			// добавим кнопку в определенный контейнер на страницу
			// параметр: array  $param      Хеш - массив с именованными параметрами (используемый в скинах)
			// параметр: string $container  Имя контейнера, куда нужно добавить кнопки
			/*$this->add_button(
			array(
			'type'    => 'link',// тип кнопки
			'label'=> 'buttontext',// Очистить
			'command'=> 'plugin.rm_duplicate_messages',// комманда onclick = "return rcmail.command('plugin.rm_duplicate_messages','',this,event)"
			'class'=> 'button clean',// класс стиля class = "button clean"
			'classact'=> 'button clean',
			'width'   => 32,// размеры
			'height'=> 32,
			'title'   => 'label1',// title = "Удалить старые сообщения"
			'domain'=> $this->ID,// id = "rcmbtn106"
			),
			// в панель управления на верху (toolbar)
			'toolbar');*/

			/**
			* Обработчик для запроса ajax.
			* Зарегистрируем обработчик для определенного действия по запросу клиента.
			* Обратный вызов будет выполнен по запросу типа /?_task=mail&_action=plugin.myaction
			* Параметр: string $action    Имя действия (должно быть уникальным)
			* Параметр: mixed  $callback Callback-Функция: строка с именем глобальной функции обратного вызова или массивом
			* в виде строки или массива со ссылкой на объект и именем метода ($obj, 'methodname')
			*/
			//$this->register_action('plugin.delallold', array($this,'clean_messages'));
		}
	}

	// основная вункция командной кнопки запускает все необходимые функции
	function functions_start()
	{
		// Переменная i номер первого письма, переменная j номер второго письма.
		$id_msg1 = 0;
		$id_msg2 = 1;

		/**
		* Инициализировать и получить объект хранения
		* 	get_storage()
		*
		* @return rcube_storage Storage Объект хранения
		*/
		$storage = $this->rc->get_storage();

		/**
		* Возвращает имя текущей папки
		* 	get_folder (): string
		*
		* @return string	Имя папки
		*/
		$folder  = $storage->get_folder();

		/**
		* Открытый метод для вывода заголовков сообщений.
		* 	list_messages(string $folder = null, int $page = null, string $sort_field = null, string $sort_order = null, int $slice) : array
		*
		* Аргументы
		* @param string $folder		Имя папки
		* @param int $page			Текущая страница в списке
		* @param string $sort_field	Поле заголовка для сортировки
		* @param string $sort_order	Порядок сортировки [ASC|DESC]
		* @param int $slice			Количество элементов среза для извлечения из массива результатов
		*
		* @return array	Индексированный массив с объектами заголовка сообщения
		*/
		$lst_msg = $storage->list_messages($folder, null, null, 'ASC', null);
		//$this->write_log_file($lst_msg);

		/**
		* В цикле перебираем массив $lst_msg и получаем uid каждого сообщения, присвоим это значение переменной $uid
		* Обход индексного массива организуем при помощи цикла for (для ассоциативных массивов предназначен специализированный оператор foreach)
		* Для подсчета количества элементов в массиве используется функция count(),
		* которая принимает в качестве параметра массив и возвращает количество элементов в нем.
		* Первый цикл (для первого сообщения) начинаем с - нуля.
		*/
		for ($id_msg1; $id_msg1 < count($lst_msg);) {

			// читаем заголовки первого сообщения в массиве $lst_msg
			$uid_msg1 = $lst_msg[$id_msg1]->uid;

			### Разбираем первое сообщение. Начало
			/**
			* Получение заголовков сообщений и структуры тела с сервера и построение структуры объекта,
			* подобной той, которая создается PEAR::Mail_mimeDecode.
			* 	get_message (int $uid, string $folder = null): object
			*
			* Аргументы
			* @param $uid int		UID сообщения для получения
			* @param $folder string	Папка для чтения
			*
			* @return object rcube_message_header Данные сообщения
			*/
			// получаем заголовки сообщения
			$msg1     = $storage->get_message($uid_msg1, $folder);
			//$this->write_log_file($msg1);

			/**
			* Получаем тело определенного сообщения с сервера
			* 	get_message_part(int $uid, string $part   = 1, \rcube_message_part $o_part = null, mixed $print = null, resource $fp = null, boolean $skip_charset_conv = false) : string
			*
			* Аргументы
			* @param int $uid					UID сообщения
			* @param string $part				Номер части
			* @param rcube_message_part $o_part	Объект детали, созданный get_structure()
			* @param mixed $print				Верно для печати части, ресурс для записи содержимого части в указатель файла
			* @param resource $fp				Указатель файла для сохранения части сообщения
			* @param boolean $skip_charset_conv	Отключает преобразование кодировки
			*
			* @return string	Сообщение / тело части, если не напечатано
			*/
			// в цикле разберём части сообщения и записываем в массив $msg_parts каждую часть в свой ключ $part
			for ($part = 0; $part < count($msg1->structure->parts); $part++) {
				$msg1_parts[$part] = $storage->get_message_part($uid_msg1, $part, null, null, null, false);
			}
			// удалим переменную $part
			unset($part);
			/// Части сообщения для сравнения в условии. Начало
			//			$msg1_subject = $lst_msg[$id_msg1]->subject;
			//			$msg1_from = $lst_msg[$id_msg1]->from;
			//			$msg1_to = $lst_msg[$id_msg1]->to;
			//			$msg1_cc = $lst_msg[$id_msg1]->cc;
			//			$msg1_replyto = $lst_msg[$id_msg1]->replyto;
			//			// отправка
			//			$msg1_date = $lst_msg[$id_msg1]->date;
			//			$msg1_timestamp = $lst_msg[$id_msg1]->timestamp;
			//			// прибытие
			//			//$msg1_internaldate = $lst_msg[$id_msg1]->internaldate;
			//			// флаги сообщения записываем в массив $msg1_flags
			//			$msg1_flags = $msg1->flags;
			//			/// Части сообщения для сравнения в условии. Конец
			//			### Разбираем первое сообщение. Конец

			// Второй цикл (для второго сообщения) начинаем с - единицы.
			for ($id_msg2; $id_msg2 < count($lst_msg);) {

				// читаем заголовки первого сообщения в массиве $lst_msg
				$uid_msg2 = $lst_msg[$id_msg2]->uid;

				### Разбираем первое сообщение. Начало
				// получаем заголовки сообщения
				$msg2     = $storage->get_message($uid_msg2, $folder);
				//$this->write_log_file($msg2);

				// в цикле разберём части сообщения и записываем в массив $msg_parts каждую часть в свой ключ $part
				for ($part = 0; $part < count($msg2->structure->parts); $part++) {
					$msg2_parts[$part] = $storage->get_message_part($uid_msg2, $part, null, null, null, false);
				}
				// удалим переменную $part
				unset($part);
				/// Части сообщения для сравнения в условии. Начало
				//				$msg2_subject = $lst_msg[$id_msg2]->subject;
				//				$msg2_from = $lst_msg[$id_msg2]->from;
				//				$msg2_to = $lst_msg[$id_msg2]->to;
				//				$msg2_cc = $lst_msg[$id_msg2]->cc;
				//				$msg2_replyto = $lst_msg[$id_msg2]->replyto;
				//				// отправка
				//				$msg2_date = $lst_msg[$id_msg2]->date;
				//				$msg2_timestamp = $lst_msg[$id_msg2]->timestamp;
				//				// прибытие
				//				//$msg2_internaldate = $lst_msg[$id_msg2]->internaldate;
				//				// флаги сообщения записываем в массив $msg2_flags
				//				$msg2_flags = $msg2->flags;
				/// Части сообщения для сравнения в условии. Конец
				//### Разбираем первое сообщение. Конец

				// условие сверки сообщений (неиспользуемые && $msg1_internaldate == $msg2_internaldate)
				if ($lst_msg[$id_msg1]->subject == $lst_msg[$id_msg2]->subject
					&& $lst_msg[$id_msg1]->from == $lst_msg[$id_msg2]->from
					&& $lst_msg[$id_msg1]->to == $lst_msg[$id_msg2]->to
					&& $lst_msg[$id_msg1]->cc == $lst_msg[$id_msg2]->cc
					&& $lst_msg[$id_msg1]->replyto == $lst_msg[$id_msg2]->replyto
					&& $lst_msg[$id_msg1]->date == $lst_msg[$id_msg2]->date
					&& $lst_msg[$id_msg1]->timestamp == $lst_msg[$id_msg2]->timestamp
				) {
					echo "Сообщения одинаковые";

					if ($msg1->flags == $msg2->flags) {
						echo "Сообщения одинаковые";
					}
				}else {
					echo "Сообщения не одинаковые";
				}
				// очищаем массивы и переменные второго сообщения, функция unset()
				unset($msg2, $msg2_parts, $uid_msg2);

				// увеличим счётчик второго сообщения
				$id_msg2++;
			}

			// очищаем массивы и переменные второго сообщения, функция unset()
			unset($msg1, $msg1_parts, $uid_msg1);
			// увеличим счётчики первого и второго сообщения
			$id_msg1++;
			$id_msg2++;
		}
		// конец программы
		echo"Закончили";
	}

	// Вставим название нашей секции. Вставим свою секцию с нашим плагином
	// Получите локализованный текст на желаемом языке
	// Обертка для rcube::gettext() с добавлением ID плагина в качестве домена
	function insert_my_section1($args)
	{
		// двумерный массив $args представляет собой строку в секции списка установленных плагинов
		$args['list']['rmduplicate'] = array(

			// ключ id - содержит название секции куда вставлять надпись - имя нашего плагина
			// массив rmduplicate содержит id и название секции (section) - куда вставляется строка с надписью названия нашего плагина
			'id'=> 'rmduplicate',

			// ключ 'label1' содержит надпись (из файла локализации) с именем нашего плагина в списке секции
			// добавляем в массив $args надпись с именем нашего плагина
			// имя получаем функцией $this->gettext('label1') из файла локализации
			'section'=> $this->gettext('label1'));
		return $args;
	}

	// Блок обработки настроек плагина (выпадающий список)
	function my_settings_blocks ($args)
	{
		// если обрабатываемая секция - 'rmduplicate', которую мы создали в предыдущей функции
		// выполняем код ниже: добавим на страницу надписи и переключатели плагина
		if ($args['section'] == 'rmduplicate') {

			// создадим два поля для ввода номеров: первое письмо и второе письмо
			// 'name' => 'имя_тега_html', 'id' => $teg_id, 'size' => размер поля (ширина)
			$first_letter = new html_inputfield(array('name'=> 'first_letter','id'  => $teg_id,'size'=> 5));
			$second_letter = new html_inputfield(array('name'=> 'second_letter','id'  => $teg_id,'size'=> 5));

			// Обертка для rcube::gettext() с добавлением ID плагина в качестве домена
			// функция $this->gettext('параметр_из_общего_массива_локализации')
			// выводим надпись на страницу "О программе" из основного файла локализации приложения
			$args['blocks']['about']['name'] = $this->gettext('about');

			// выводим надпись из метки 'label2', из файла локализации плагина
			$args['blocks']['about']['content'] = $this->gettext('label2');

			// Блок "Основные настройки". Начало
			// Выводим надпись "Основные настройки", из основного файла локализации приложения
			$args['blocks']['main']['name'] = $this->gettext('mainoptions');

			// номер первого письма
			$args['blocks']['main']['options']['first_letter'] = array(

				// выводим надпись из метки 'label3', из файла локализации плагина
				'title'=> $this->gettext('label3'),

				// текстовое поле с номером первого письма
				'content'=> $first_letter->show(1) //"текстовое поле с номером первого письма" //$this->gettext('')
			);

			// номер второго письма
			$args['blocks']['main']['options']['second_letter'] = array(

				// выводим надпись из метки 'label4', из файла локализации плагина
				'title'=> $this->gettext('label4'),

				// текстовое поле с номером второго письма
				'content'=> $second_letter->show(2) //"текстовое поле с номером второго письма" //$this->gettext('')
			);
			// Блок "Основные настройки". Конец

			// Блок "Выводная информация о работе плагина". Начало
			// Выводим надпись из метки 'label5', из файла локализации плагина
			$args['blocks']['letters']['name'] = $this->gettext('label5');

			// номер первого письма
			$args['blocks']['letters']['options']['first_current_letter'] = array(

				// выводим надпись из метки 'label3', из файла локализации плагина
				'title'=> $this->gettext('label3'),

				// текстовое поле с номером первого письма
				'content'=> 123 // "надпись с номером первого письма" //$this->gettext('')
			);

			// номер второго письма
			$args['blocks']['letters']['options']['second_current_letter'] = array(

				// выводим надпись из метки 'label4', из файла локализации плагина
				'title'=> $this->gettext('label4'),

				// текстовое поле с номером второго письма
				'content'=> 456 // "надпись с номером второго письма" //$this->gettext('')
			);

			foreach ($list_folder as  $val) {
				echo  $val;  // выведет 123
			}

			// Выводим надпись из метки 'label5', из файла локализации плагина
			$args['blocks']['list_folders']['name'] = "Список папок"; // $this->gettext('label5');

			// список папок $list_folders
			$args['blocks']['list_folders']['options'] = array(

				// выводим надпись из метки 'label4', из файла локализации плагина
				'title'=> "Список папок",

				// список папок
				'content'=> 1
			);
			// Блок "Основные настройки". Конец

			// вызываем функцию записи лог - файла
			//$this->write_log_file('sections ', $args);
		}
		return $args;
	}

	// Функция интеграции скина нашего плагина, в общий скин системы.
	private function includeCSS ()
	{
		//Получаем текущий системный скин
		$skin_path = $this->local_skin_path();

		//Проверяем если путь к файлу скина плагина сформирован
		if (is_file($this->home . "/$skin_path/rm_duplicate_messages.css")) {

			// То добавляем его в текущую HTML - страницу
			$this->include_stylesheet("$skin_path/rm_duplicate_messages.css");
		} else {

			// Иначе добавляем его в текущую HTML - страницу другим способом
			$this->include_stylesheet('css/rm_duplicate_messages.css');
		}
	}

	/**
	* Пишем отладочную информацию в log-файл.
	* file_put_contents — Пишет данные в файл
	* print_r — Выводит удобочитаемую информацию о переменной
	* Если filename не существует, файл будет создан.
	* Иначе, существующий файл будет перезаписан, за исключением случая,
	* если указан флаг FILE_APPEND
	*/
	protected function write_log_file ($args)
	{
		file_put_contents(
			$this->home . '/logs/rmduplicate.log',
			print_r($args, true),
			//print_r( / n, true),
			FILE_APPEND);
	}

	private function logger ($src, $args)
	{
		file_put_contents('LOG.txt', $src . print_r($args, true), FILE_APPEND);
	}
}