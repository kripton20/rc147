<?php

//Расширяем наш класс от класса rcube_plugin
class rm_duplicate_messages extends rcube_plugin
{
	// Объявляем глобальные переменные
	//
	/**
	* Инициализация плагина.
	*/
	function init ()
	{
		/**
		* Это реализует шаблон проектирования singleton.
		*
		* @param integer $mode Игнорируемый аргумент rcube :: get_instance ()
		* @param string  $env  Имя среды для запуска (например, live, dev, test)
		*
		* @return rcmail Единственный и неповторимый экземпляр
		*/
		// переменная $this относится к текущему классу и представляет собой неявный объект.
		// rc - свойство этого объекта. Запишем туда системные настройки приложения.
		$this->rc = rcmail::get_instance();
		// переменной $tsk присвоим имя текущей задачи приложения
		//$tsk = $this->rc->task;
		// если задача 'mail' и действие '' или 'list', покажем нашу кнопку на панели, в других случаях не показываем
		if ($this->rc->task == 'mail' && ($this->rc->action == '' || $this->rc->action == 'list')) {
			/**
			* Загрузка локализованных текстов из каталога обрабатываемого плагина.
			*
			* @param string $dir        Каталог для поиска
			* @param mixed  $add2client Сделать тексты доступными на клиенте (массив со списком или true для всех)
			*/
			// вызываем функцию локализации - add_texts() из родительского класса интерфейса плагинов - rcube_plugin
			// Файл локализации добавляется в общий массив $texts, в массиве находятся ярлыки добавляемые клиенту
			// localization - это имя папки, в массиве указываем ключи из массива файла локализации
			// метод add_texts() записывает файл локализации нашего плагина в общий массив локализации
			$this->add_texts('localization', array('label1','label2','checkdpl','successful'));
			// Функция интеграции скина нашего плагина, в общий скин системы. Загружаем файл скина плагина
			$this->includeCSS();
			/**
			* Клиентские скрипты и элементы пользовательского интерфейса.
			* Конечно, плагины имеют большее отношение, чем просто отслеживание событий на стороне сервера.
			* API плагина также позволяет расширить пользовательский интерфейс и функциональность клиента.
			* Первый шаг – добавить код JavaScript на определенную страницу/действие.
			* Сделаем этот файл javascript доступным для клиента
			* Создадим файл сценария в папке вашего плагина, а затем включите его в init() метод вашего класса плагина с помощью
			* $this->include_script('client.js');
			*
			* @param string $fn путь к файлу; абсолютный или относительный к каталогу плагина
			*/
			// добавим код JavaScript на определенную страницу / действие, создадим файл сценария в папке вашего плагина,
			// а затем включим его в init() метод вашего класса плагина с помощью $this->include_script()
			$this->include_script('rm_duplicate_messages.js');
			/**
			* Добавим кнопку в определенный контейнер на страницу (в панель управления на верху, toolbar)
			*
			* @param array  $param      Хеш-массив с именованными параметрами (используемый в скинах)
			* @param string $container  Имя контейнера, куда нужно добавить кнопки
			*/
			// добавим командную кнопку на страницу в контейнер toolbar
			$this->add_button(
				array(
					'domain'  => $this->ID,// id = "rcmbtn106"
					'type'=> 'link',// тип кнопки
					'label'=> 'label6',// локализованная надпись на кнопке
					'title'=> 'label7',// локализованная всплывающая подсказка
					'command'=> 'plugin.btn_cmd_rm_dublecates',// имя выполняемой команды для кнопки
					'width'=> 32,// ширина
					'height'=> 32,// высота
					'class'=> 'button btn_cmd',// класс стиля командной кнопки
					'classact'=> 'button btn_cmd',// класс стиля командной кнопки в нажатом, активном состоянии
				),
				'toolbar'); // панель управления - на верху (toolbar)
			/**
			* Зарегистрируем обработчик для определенного действия ajax (запроса) от клиента.
			* Обратный вызов будет выполнен по запросу типа /?_task=mail&_action=plugin.action
			* @param string $action   Имя действия (_task = mail& _action = plugin.action) (должно быть уникальным)
			* @param mixed  $callback Callback-Функция обратного вызова в виде строки со ссылкой на объект и именем метода:
			* 						  строка с именем глобальной функции (или массивом) обратного вызова ($obj, 'methodname')
			* 						  или массив со ссылкой на объект и именем метода
			* @param mixed  $callback Функция обратного вызова в виде строки или массив со ссылкой на объект и именем метода
			* @param string $owner    Имя плагина, регистрирующего это действие
			* @param string $task     Имя задачи, зарегистрированное этим плагином
			* Пример: $this->register_action('$action', $callback'));
			*         $this->register_action('$action',  array($this,'function'));
			*/
			$this->register_action('plugin.rm_dublecates', array($this,'rm_dublecates'));
		}
		// когда наша функция запускается страница обновляется функцию обратного вызова требуется зарегистрировать еще раз
		elseif ($this->rc->action == 'plugin.rm_dublecates') {
			$this->register_action('plugin.rm_dublecates', array($this,'rm_dublecates'));
		}
		// в условии проверяем имя текущей задачи приложения, если 'settings' - делаем меню настроек нашего плагина
		//if ($tsk == 'settings') {}
	}

	// основная вункция командной кнопки запускает все необходимые функции
	function rm_dublecates()
	{
		/**
		* Эта функция реализует шаблон проектирования singleton
		*
		* @param int    $mode Параметры для инициализации этим экземпляром. См. Константы rcube::INIT_WITH_ *
		* @param string $env  Имя среды для запуска (например, live, dev, test)
		*
		* @return rcube Единственный экземпляр
		*/
		//$this->rc = rcmail::get_instance();
		//$rcube = rcube::get_instance();
		//$storage = $rcube->get_storage(); // = $rcube->get_storage();
		//$storage = $rcube->get_storage;
		//$storageth = $this->rc->get_storage();
		//$rcmail = rcmail::get_instance();
		/**
		* Инициализировать и получить объект хранения
		*
		* 	get_storage()
		*
		* @return rcube_storage Storage Объект хранения
		*/
		$storage = $this->rc->get_storage();

		/**
		* Возвращает имя текущей папки
		*
		* 	get_folder (): string
		*
		* @return string Имя папки
		*/
		$folder  = $storage->get_folder();

		/**
		* Открытый метод для вывода заголовков сообщений.
		* 	list_messages(string $folder = null, int $page = null, string $sort_field = null, string $sort_order = null, int $slice) : array
		*

		* @param string $folder		Имя папки
		* @param int $page			Текущая страница в списке
		* @param string $sort_field	Поле заголовка для сортировки
		* @param string $sort_order	Порядок сортировки [ASC|DESC]
		* @param int $slice			Количество элементов среза для извлечения из массива результатов
		*
		* @return array	Индексированный массив с объектами заголовка сообщения
		*/
		$lst_msg = $storage->list_messages($folder, null, null, 'ASC', null);
		//		$lst_msg1 = $storage->folder_data($folder);
		//		$lst_msg1 = $storage->index($folder, $sort_field = null, $sort_order = null);

		//$this->write_log_file($lst_msg);
		// переменные $msg1_id и $msg2_id номера первого и второго сообщения в массиве $lst_msg
		$msg1_id = 0;
		$msg2_id = 1;
		/**
		* В цикле перебираем массив $lst_msg и получаем uid каждого сообщения, присвоим это значение переменной $uid
		* Обход индексного массива организуем при помощи цикла for (для ассоциативных массивов предназначен специализированный оператор foreach)
		* Для подсчета количества элементов в массиве используется функция count(),
		* которая принимает в качестве параметра массив и возвращает количество элементов в нем.
		* Первый цикл (для первого сообщения) начинаем с - нуля.
		*/
		foreach ($lst_msg as $msg1_header) {
			//for ($msg1_id; $msg1_id < count($lst_msg);) {
			// читаем заголовки первого сообщения в массиве $lst_msg
			//$msg1_uid = $lst_msg[$msg1_id]->uid;
			$msg1_uid = $msg1_header->uid;
			/// Разбираем первое сообщение. Начало
			/**
			* Получение заголовков сообщений и структуры тела с сервера и построение структуры объекта,
			* подобной той, которая создается PEAR::Mail_mimeDecode
			*
			* 	get_message (int $uid, string $folder = null): object
			*

			* @param int $uid		UID сообщения для получения
			* @param string	$folder Папка для чтения
			*
			* @return object rcube_message_header Данные сообщения
			*/
			// получаем заголовки сообщения
			$msg1     = $storage->get_message($msg1_uid, $folder);
			/**
						* Снять флаг сообщения для одного или нескольких сообщений
						*
						* @param mixed $uids 	UID сообщений в виде массива или строки, разделенной запятыми, или '*'
						* @param string $flag 	Флаг, который нужно снять: SEEN, DELETED, RECENT, ANSWERED, DRAFT, MDNSENT
						* @param string $folder Имя папки
						*
						* @return bool Статус операции
						* @ см. set_flag
						*/
						$storage->unset_flag($msg1_uid, 'ANSWERED', $folder, true);
						$storage->unset_flag($msg1_uid, 'FLAGGED', $folder, true);
						$storage->unset_flag($msg1_uid, 'FORWARDED', $folder, true);
						$storage->unset_flag($msg1_uid, 'DELETED', $folder, true);
						$storage->unset_flag($msg1_uid, 'DUBLIKAT', $folder, true);
						$a=1;
			/**
			* Получаем тело определенного сообщения с сервера
			*
			* 	get_message_part(int $uid, string $part   = 1, \rcube_message_part $o_part = null, mixed $print = null, resource $fp = null, boolean $skip_charset_conv = false) : string
			*

			* @param int $uid					UID сообщения
			* @param string $part				Номер части
			* @param rcube_message_part $o_part	Объект детали, созданный get_structure()
			* @param mixed $print				Верно для печати части, ресурс для записи содержимого части в указатель файла
			* @param resource $fp				Указатель файла для сохранения части сообщения
			* @param boolean $skip_charset_conv	Отключает преобразование кодировки
			*
			* @return string	Сообщение / тело части, если не напечатано
			*/
			// в цикле разберём части сообщения и записываем в массив $msg1_parts каждую часть в свой ключ $part,
			// если частей нет - PHP выдаёт предупреждение 'Invalid argument supplied for foreach()' - нет переменной $value
			foreach ($msg1->structure->parts as $part => $value) {
				$msg1_parts[$part] = $storage->get_message_part($msg1_uid, $part, null, null, null, false);
			}
			//			for ($part = 0; $part < count($msg1->structure->parts); $part++) {
			//				$msg1_parts3[$part] = $storage->get_message_part($msg1_uid, $part, null, null, null, false);
			//			}
			// удалим переменную $part
			unset($part, $value);
			/// Разбираем первое сообщение. Конец
			// Второй цикл (для второго сообщения) начинаем с - единицы.
			for ($msg2_id; $msg2_id < count($lst_msg);) {
				// читаем заголовки первого сообщения в массиве $lst_msg
				$msg2_uid = $lst_msg[$msg2_id]->uid;
				// Разбираем второе сообщение. Начало
				// получаем заголовки сообщения
				$msg2     = $storage->get_message($msg2_uid, $folder);
				/**
						* Снять флаг сообщения для одного или нескольких сообщений
						*
						* @param mixed $uids 	UID сообщений в виде массива или строки, разделенной запятыми, или '*'
						* @param string $flag 	Флаг, который нужно снять: SEEN, DELETED, RECENT, ANSWERED, DRAFT, MDNSENT
						* @param string $folder Имя папки
						*
						* @return bool Статус операции
						* @ см. set_flag
						*/
						$storage->unset_flag($msg2_uid, 'ANSWERED', $folder, true);
						$storage->unset_flag($msg2_uid, 'FLAGGED', $folder, true);
						$storage->unset_flag($msg2_uid, 'FORWARDED', $folder, true);
						$storage->unset_flag($msg2_uid, 'DELETED', $folder, true);
						$storage->unset_flag($msg2_uid, 'DUBLIKAT', $folder, true);
						$a=1;
						
				$msg2_date= $msg2->date;
				//$this->write_log_file($msg2);
				// в цикле разберём части сообщения и записываем в массив $msg2_parts каждую часть в свой ключ $part,
				// если частей нет - PHP выдаёт предупреждение 'Invalid argument supplied for foreach()' - нет переменной $value
				foreach ($msg2->structure->parts as $part => $value) {
					$msg2_parts[$part] = $storage->get_message_part($msg2_uid, $part, null, null, null, false);
				}
				//				for ($part = 0; $part < count($msg2->structure->parts); $part++) {
				//					$msg2_parts[$part] = $storage->get_message_part($msg2_uid, $part, null, null, null, false);
				//				}
				// удалим переменную $part
				unset($part, $value);
				/// Разбираем второе сообщение. Конец
				// условие сверки сообщений (неиспользуемые Заголовок сообщения In - Reply - To in_reply_to)
				//     Тема сообщения
				if ($lst_msg[$msg1_id]->subject == $lst_msg[$msg2_id]->subject
					// Отправитель сообщения (От)
					&& $lst_msg[$msg1_id]->from == $lst_msg[$msg2_id]->from
					// Получатель сообщения (Кому)
					&& $lst_msg[$msg1_id]->to == $lst_msg[$msg2_id]->to
					// Дополнительные получатели сообщения (Копия)
					&& $lst_msg[$msg1_id]->cc == $lst_msg[$msg2_id]->cc
					// Заголовок ответа на сообщение
					&& $lst_msg[$msg1_id]->replyto == $lst_msg[$msg2_id]->replyto
					// Дата сообщения (Дата)
					&& $lst_msg[$msg1_id]->date == $lst_msg[$msg2_id]->date
					// Отметка времени сообщения (на основе даты сообщения)
					&& $lst_msg[$msg1_id]->timestamp == $lst_msg[$msg2_id]->timestamp
					// Внутренняя дата IMAP
					//&& $lst_msg[$msg1_id]->internaldate == $lst_msg[$msg2_id]->internaldate
				) {
					// проверяем флаги сообщений, если флаги одинаковые - удаляем второе сообщение
					if ($msg1->flags == $msg2->flags) {
						// если Флаги сообщения одинаковые - выделяем дублирующееся сообщение в списке
						// установим флаг 'DELETED' в сообщение
						//						$msg2_flags = array(
						//							'DELETED' => 'TRUE','DUBLIKAT'=> 'TRUE',
						//							'FLAGGED' => 'TRUE','DUBLIKAT'=> 'TRUE');
						//$msg2->flags = array('DELETED' => 'TRUE','DUBLIKAT'=> 'TRUE');
						//$msg2->flags = array('FLAGGED' => 'TRUE','DUBLIKAT'=> 'TRUE');
						//$storage->set_flag($msg2_id, 'DELETED', $folder);
						//$storage->set_flag($msg2_id, 'FLAGGED', $folder);
						/**
						* Возвращает заголовки сообщения в виде строки.
						*
						* get_raw_headers(int $uid) : string
						*
						* @param int $uid 	UID сообщения
						*
						* @return string	Строка заголовков сообщений
						*/
						//$msg2_raw_headers = $storage->get_raw_headers($msg2_uid);
						/**
						* Возвращает весь источник сообщения в виде строки (или сохраняет в файл).
						*
						* get_raw_body(int $uid, resource $fp = null) : string
						*
						* @param $uid int		UID сообщения
						* @param $fp resource	Указатель файла для сохранения сообщения
						*
						* @return string	Строка источника сообщения
						*/
						//$msg2_raw_body = $storage->get_raw_body($msg2_uid);

						/**
						* Установить флаг сообщения для одного или нескольких сообщений
						*
						* @param mixed $uids			UID сообщений в виде массива или строки, разделенной запятыми, или '*'
						* @param string $flag			Флаг для установки: SEEN, UNDELETED, DELETED, RECENT, ANSWERED, DRAFT, MDNSENT
						* @param string $folder		Имя папки
						* @param boolean $skip_cache	Истина, чтобы пропустить очистку кеша сообщений
						*
						* @return boolean Статус операции
						*/
						//$storage->set_flag($msg2_uid, 'DUBLIKAT', $folder, true);
						//$storage->set_flag($msg2_uid, 'DELETED', $folder, true);
						
						/**
						* Добавить почтовое сообщение (источник) в определенную папку.
						*
						* save_message(string $folder, string|array &$message, string $headers = '', boolean $is_file = false, array $flags = array(), mixed $date = null) : int|bool
						*
						* @param $folder string			Целевая папка
						* @param $message string|array	Строка или имя файла источника сообщения или массив (строк и указателей файлов)
						* @param $headers string		Строка заголовков, если $message содержит только тело
						* @param $is_file boolean		Истинно, если $message - имя файла
						* @param $flags array			Флаги сообщений
						* @param $date mixed			Внутренняя дата сообщения
						*
						* @return int|bool	Добавленный UID сообщения или True в случае успеха, False в случае ошибки
						*/
						// сохраняем сообщение
						//$msg2_result = $storage->save_message($folder, & $msg2_raw_body, $msg2_raw_headers, false, $msg2_flags, $msg2_date);
						//$msg2_result = $storage->save_message($folder, $msg2_raw_body, $msg2_raw_headers, false, $msg2_flags, $msg2_date);

					}
					// если у второго сообщения установлен флаг:
					// 'ANSWERED', 'FLAGGED' или 'FORWARDED' то удаляем первое сообщение
					elseif (isset($msg2->flags['ANSWERED'])
						|| isset($msg2->flags['FLAGGED'])
						|| isset($msg2->flags['FORWARDED'])) {
						//echo "Флаги сообщения разные: - установлен флаг 'ANSWERED', 'FLAGGED' или 'FORWARDED'";
						// выделяем дублирующееся сообщение в списке
						//						$msg1_flags = array(
						//							'DELETED' => 'TRUE','DUBLIKAT'=> 'TRUE',
						//							'FLAGGED' => 'TRUE','DUBLIKAT'=> 'TRUE');
						//$msg1->flags = array('DELETED' => 'TRUE','DUBLIKAT'=> 'TRUE');
						//$msg1->flags = array('FLAGGED' => 'TRUE','DUBLIKAT'=> 'TRUE');
						//$storage->set_flag($msg1_id, 'DELETED', $folder);
						//$storage->set_flag($msg1_id, 'FLAGGED', $folder);
						// получаем заголовки и сообщение
						//$msg1_raw_headers = $storage->get_raw_headers($msg1_uid);
						//$msg1_raw_body = $storage->get_raw_body($msg1_uid);
						// сохраняем сообщение
						//$msg1_result = $storage->save_message($folder, & $msg1_raw_body, $msg1_raw_headers, false, $msg1_flags, $msg1_date);
						//$msg2_result = $storage->save_message();
						//$storage->set_flag($msg2_uid, 'DUBLIKAT', $folder, true);
						//$storage->set_flag($msg2_uid, 'DELETED', $folder, true);
						//$storage->set_flag($msg2_uid, '', $folder, true);
						
					}
				}
				else {
					// если сообщения не одинаковые - ничего не делаем
				}
				// очищаем массивы и переменные второго сообщения, функция unset()
				unset($msg2, $msg2_parts, $msg2_uid);
				// увеличим счётчик второго сообщения
				$msg2_id++;
			}
			// очищаем массивы и переменные второго сообщения, функция unset()
			unset($msg1, $msg1_header, $msg1_parts, $msg1_uid);
			// увеличим счётчики первого и второго сообщения и повторяем весь цикл
			$msg1_id++;
			$msg2_id = $msg1_id + 1;
		}
		// добавим локализованную метку в клиентскую среду
		$this->rc->output->add_label('plugin.checkdpl', 'plugin.successful');
		/**
		* Вызов клиентского метода
		*
		* @param string	Метод для вызова
		* @param ...	Дополнительные аргументы
		*
		* команда выполняется после функции - send()
		*/
		$this->rc->output->command('plugin.successful');
		// функция отправки вывода клиенту, и работа PHP - скрипта заканчивается
		$this->rc->output->send();
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

	// Функция интеграции скина нашего плагина, в общий скин системы
	private function includeCSS ()
	{
		//Получаем текущий системный скин
		$skin_path = $this->local_skin_path();
		//Проверяем если путь к файлу скина плагина сформирован
		if (is_file($this->home . "/$skin_path/rm_duplicate_messages.css")) {
			// То добавляем его в текущую HTML - страницу
			$this->include_stylesheet("$skin_path/rm_duplicate_messages.css");
		}
		else {
			// Иначе добавляем его в текущую HTML - страницу другим способом
			$this->include_stylesheet('css/rm_duplicate_messages.css');
		}
	}

	/**
	* Пишем отладочную информацию в log-файл.
	* file_put_contents — Пишет данные в файл
	* print_r — Выводит удобочитаемую информацию о переменной
	* Если filename не существует, файл будет создан.
	* Иначе, существующий файл будет перезаписан, за исключением случая, если указан флаг FILE_APPEND
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