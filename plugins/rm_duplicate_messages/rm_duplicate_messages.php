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
			*
			* Вызываем функцию локализации - add_texts() из родительского класса интерфейса плагинов - rcube_plugin
			* Файл локализации добавляется в общий массив $texts, в массиве находятся ярлыки добавляемые клиенту
			* localization - это имя папки, в массиве указываем ключи из массива файла локализации
			* Метод add_texts() записывает файл локализации нашего плагина в общий массив локализации
			*/
			// добавим наши локализованные метки на страницу настроек плагина
			$this->add_texts('localization', array(
					'label1',
					'label2',
					'label3',
					'label4',
					'label5',
					'label6',
					'label7',
					'label8',
					'label9',
					'label10',
					'label11',
					'checkdpl',
					'successful'
				));
			// Функция интеграции скина нашего плагина, в общий скин системы. Загружаем файл скина плагина
			//$this->includeCSS();
			$this->include_stylesheet($this->local_skin_path() . '/rm_duplicate_messages.css');
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
					'label'=> 'label1',// локализованная надпись на кнопке
					'title'=> 'label2',// локализованная всплывающая подсказка
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
			*                           строка с именем глобальной функции (или массивом) обратного вызова ($obj, 'methodname')
			*                           или массив со ссылкой на объект и именем метода
			* @param mixed  $callback Функция обратного вызова в виде строки или массив со ссылкой на объект и именем метода
			* @param string $owner    Имя плагина, регистрирующего это действие
			* @param string $task     Имя задачи, зарегистрированное этим плагином
			* Пример: $this->register_action('$action', $callback'));
			*         $this->register_action('$action',  array($this,'function'));
			*/
			$this->register_action('plugin.rm_dublecates', array($this,'rm_dublecates'));
		}
		// когда наша функция запускается - страница обновляется, функцию обратного вызова требуется зарегистрировать еще раз
		elseif ($this->rc->action == 'plugin.rm_dublecates') {
			$this->register_action('plugin.rm_dublecates', array($this,'rm_dublecates'));
		}
		// в условии проверяем имя текущей задачи приложения, если 'settings' - делаем меню настроек нашего плагина
		if ($this->rc->task == 'settings') {
			//			$this->add_texts('localization', array('label1','label2'));
			//			// загружаем файл скина плагина
			//			//$this->includeCSS();
			//			$this->include_stylesheet($this->local_skin_path() . ' / rm_duplicate_messages.css');
			//			/**
			// * Регистрируем хуки сервера
			// * Способ работы хуков плагинов заключается в том, что в разное время, пока Roundcube обрабатывает, он проверяет,
			// * есть - ли у каких - либо плагинов зарегистрированные функции для запуска в это время, и если да, то функции запускаются
			// * (путем выполнения «ловушки»). Эти функции могут изменять или расширять поведение Roundcube по умолчанию.
			// * Регистрация хуков:     $this->add_hook('hook_name', $callback_function);
			// * где первый аргумент – это имя раздела куда мы хотим поместить наш хук, хуки содержатся здесь: $this->handlers
			// * где второй аргумент – это обратный вызов PHP (функция в этом файле ниже), который может ссылаться на простую функцию или метод
			// * объекта. Зарегистрированная функция получает один хеш - массив в качестве аргумента, который содержит определенные данные текущего
			// * контекста в зависимости от ловушки.
			// * См. «Перехватчики подключаемых модулей» для получения полного описания всех перехватчиков и их полей аргументов.
			// * Аргумент var может быть изменен функцией обратного вызова и может (даже частично) быть возвращен приложению.
			//			*/
			//			// Поместим наш хук в секцию preferences_sections_list
			//			// Позволяет плагину изменять список разделов пользовательских настроек
			//			// потом поместим наш хук ещё в другую секцию https://github.com / roundcube / roundcubemail / wiki / Plugin - Hooks
			//			// хуки помещаются в массив $this->$api->$handlers
			//			### Эти два хука добавляют плагин в секцию.
			//			$this->add_hook('preferences_sections_list', array($this,'insert_section'));
			//			$this->add_hook('preferences_list', array($this,'settings_blocks'));
			//
			//			//$this->add_hook('preferences_save', array($this,'save_settings'));
			//
			//			// получаем список папок почтового ящика
			//			//$this->add_hook('render_mailboxlist', array($this,'my_render_mailboxlist1'));
			//			$a = 1; // для остановки
		}
	}

	// основная вункция командной кнопки запускает все необходимые функции
	function rm_dublecates()
	{
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
		* @param string $folder        Имя папки
		* @param int $page             Текущая страница в списке
		* @param string $sort_field    Поле заголовка для сортировки
		* @param string $sort_order    Порядок сортировки [ASC|DESC]
		* @param int $slice            Количество элементов среза для извлечения из массива результатов
		*
		* @return array    Индексированный массив с объектами заголовка сообщения
		*/
		// долго строит
		$lst_msg = $storage->list_messages($folder, null, null, 'ASC', null);

		// вызываем функцию записи лог - файла
		//$this->write_log_file($lst_msg);

		// переменные $msg1_id и $msg2_id номера первого и второго сообщения в массиве $lst_msg
		$msg1_id    = 0;
		$msg2_id    = 1;
		// колличество отмеченных сообщений
		$msg_marked = 0;

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
			//$msg1_uid = $msg1_header->uid;
			/// Разбираем первое сообщение. Начало
			/**
			* Получение заголовков сообщений и структуры тела с сервера и построение структуры объекта,
			* подобной той, которая создается PEAR::Mail_mimeDecode
			*
			* 	get_message (int $uid, string $folder = null): object
			*
			* @param int $uid        UID сообщения для получения
			* @param string    $folder Папка для чтения
			*
			* @return object rcube_message_header Данные сообщения
			*/
			// получаем заголовки сообщения
			$msg1     = $storage->get_message($msg1_uid, $folder);

			// если сообщение имеет флаг 'DUBLIKAT' - пропустим это сообщение (начнём новую интерацию цикла)
			if (isset($msg1->flags['DUBLIKAT'])) {
				// увеличим счётчики первого и второго сообщения и повторяем весь цикл
				$msg1_id++;
				$msg2_id = $msg1_id + 1;
				// начнём цикл заново
				continue;
			}

			/**
			* Получаем тело определенного сообщения с сервера
			*
			* 	get_message_part(int $uid, string $part   = 1, \rcube_message_part $o_part = null, mixed $print = null, resource $fp = null, boolean $skip_charset_conv = false) : string
			*
			* @param int $uid                    UID сообщения
			* @param string $part                Номер части
			* @param rcube_message_part $o_part    Объект детали, созданный get_structure()
			* @param mixed $print                Верно для печати части, ресурс для записи содержимого части в указатель файла
			* @param resource $fp                Указатель файла для сохранения части сообщения
			* @param boolean $skip_charset_conv    Отключает преобразование кодировки
			*
			* @return string    Сообщение / тело части, если не напечатано
			*/
			// в цикле разберём части сообщения и записываем в массив $msg1_parts каждую часть в свой ключ $part,
			// если частей нет - PHP выдаёт предупреждение 'Invalid argument supplied for foreach()' - нет переменной $value
//			foreach ($msg1->structure->parts as $part => $msg1_part) {
//				// долго
//				$msg1_parts[$part] = $storage->get_message_part($msg1_uid, $part, null, null, null, false);
//			}

			// удалим переменые
			unset($part, $msg1_part);
			/// Разбираем первое сообщение. Конец

			// Второй цикл (для второго сообщения) начинаем с - единицы.
			for ($msg2_id; $msg2_id < count($lst_msg);) {
				// читаем заголовки первого сообщения в массиве $lst_msg
				$msg2_uid = $lst_msg[$msg2_id]->uid;

				/// Разбираем второе сообщение. Начало.
				// получаем заголовки сообщения
				$msg2     = $storage->get_message($msg2_uid, $folder);

				// если сообщение имеет флаг 'DUBLIKAT' - пропустим это сообщение (начнём новую интерацию цикла)
				if (isset($msg2->flags['DUBLIKAT'])) {
					// увеличим счётчик второго сообщения
					$msg2_id++;
					// начнём цикл заново
					continue;
				}

				// вызываем функцию записи лог - файла
				//$this->write_log_file($msg2);

				// в цикле разберём части сообщения и записываем в массив $msg2_parts каждую часть в свой ключ $part,
				// если частей нет - PHP выдаёт предупреждение 'Invalid argument supplied for foreach()' - нет переменной $value
//				foreach ($msg2->structure->parts as $part => $msg2_part) {
//					// долго
//					$msg2_parts[$part] = $storage->get_message_part($msg2_uid, $part, null, null, null, false);
//				}

				//  удалим переменые
				unset($part, $msg2_part);
				/// Разбираем второе сообщение. Конец

				// условие сверки сообщений
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
					// Заголовок сообщения In - Reply - To
					&& $lst_msg[$msg1_id]->in_reply_to == $lst_msg[$msg2_id]->in_reply_to
					// Части сообщений
					&& $msg1_parts == $msg2_parts
				) {
					// проверяем флаги сообщений: если флаги одинаковые то установим флаг 'DELETED' на второе сообщение
					if ($msg1->flags == $msg2->flags) {
						// установим флаг на дублирующееся сообщение
						$storage->set_flag($msg2_uid, 'DUBLIKAT', $folder, true);
						$storage->set_flag($msg2_uid, 'DELETED', $folder, true);
						// подсчитываем колличество отмеченных сообщений
						$msg_marked = $msg_marked + 1;
						// если у второго сообщения установлен флаг: 'ANSWERED', 'FLAGGED' или 'FORWARDED' то -
						// установим флаг 'DELETED' во первое сообщение
					}elseif ((isset($msg2->flags['ANSWERED']) || isset($msg2->flags['FLAGGED']) || isset($msg2->flags['FORWARDED']))
						&& (!isset($msg1->flags['ANSWERED']) || (!isset($msg1->flags['FLAGGED'])) || (!isset($msg1->flags['FORWARDED'])))) {
						// если сообщение имеет флаг 'ANSWERED' или 'FLAGGED' или 'FORWARDED' - пропускаем это сообщение
						if (isset($msg1->flags['ANSWERED']) || (isset($msg1->flags['FLAGGED'])) || (isset($msg1->flags['FORWARDED']))) {
							// очищаем массивы и переменные второго сообщения, функция unset()
							unset($msg2, $msg2_parts, $msg2_uid);
							// выходим из текущего цикла
							break;
						}
						// установим флаг на дублирующееся сообщение
						$storage->set_flag($msg2_uid, 'DUBLIKAT', $folder, true);
						$storage->set_flag($msg2_uid, 'DELETED', $folder, true);
						// подсчитываем колличество отмеченных сообщений
						$msg_marked = $msg_marked + 1;
						// очищаем массивы и переменные второго сообщения, функция unset()
						unset($msg2, $msg2_parts, $msg2_uid);
						// выходим из текущего цикла
						break;
						// помечаем сообщение как дуюликат при обычном условии сравнения
					}else {
						/**
						* Установим флаг сообщения для одного или нескольких сообщений
						*
						* @param mixed $uids            UID сообщений в виде массива или строки, разделенной запятыми, или '*'
						* @param string $flag            Флаг для установки: SEEN, UNSEEN, DELETED, UNDELETED, RECENT, ANSWERED, DRAFT, MDNSENT
						* @param string $folder            Имя папки
						* @param boolean $skip_cache    Истина, чтобы пропустить очистку кеша сообщений
						*
						* @return boolean Статус операции
						*/
						// установим флаг на дублирующееся сообщение
						$storage->set_flag($msg2_uid, 'DUBLIKAT', $folder, true);
						$storage->set_flag($msg2_uid, 'DELETED', $folder, true);
						// подсчитываем колличество отмеченных сообщений
						$msg_marked = $msg_marked + 1;
					}
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
		* Установить переменную среды
		*
		* @param string $name Имя свойства
		* @param mixed $value Значение свойства
		*/
		// передадим значение переменной в клиентскую среду (браузер)
		$this->rc->output->set_env('msg_marked', $msg_marked);

		/**
		* Вызов клиентского метода
		*
		* @param string Метод для вызова
		* @param ...	Дополнительные аргументы
		*
		* Команда выполняется после функции - send()
		*/
		$this->rc->output->command('plugin.successful');

		/**
		* Отправить вывод клиенту.
		* Функция отправки вывода клиенту, после этого работа PHP-скрипта заканчивается
		*/
		$this->rc->output->send();
	}

	// вставим название нашей секции. Вставим свою секцию с нашим плагином
	// получите локализованный текст на желаемом языке
	function insert_section($args)
	{
		//		/**
		// * Получаем локализованный текст на желаемом языке
		// *
		// * @param mixed  $attrib Массив именованных параметров или имя метки
		// * @param string $domain Метка домена (имя плагина)
		// *
		// * @return string Локализованный текст
		// *
		// * @ см. rcube::gettext()
		// * Функция $this->gettext('параметр_из_общего_массива_локализации')
		// *
		// * Двумерный массив $args представляет собой строку в секции списка установленных плагинов
		// * Подмассив rmduplicate содержит два ключа: id и section
		// * ключ id - содержит название секции куда вставляется строка с надписью - название нашего плагина
		// * ключ section - содержит надпись (label из файла локализации) с именем нашего плагина в списке секции.
		//		*/
		//		// добавляем в массив $args надпись с именем нашего плагина, имя получаем функцией $this->gettext('label1') из файла локализации
		//		$args['list']['rmduplicate'] = array('id'     => 'rmduplicate','section'=> $this->gettext('label1'));
		//		return $args;
	}

	// Блок обработки настроек плагина (выпадающий список)
	function settings_blocks ($args)
	{
		//		// если обрабатываемая секция = 'rmduplicate', которую мы создали в предыдущей функции то
		//		// выполняем код ниже: добавим на страницу надписи и переключатели плагина
		//		if ($args['section'] == 'rmduplicate') {
		//			// выводим надпись на страницу "О программе" из основного файла локализации приложения
		//			$args['blocks']['about']['name'] = $this->gettext('about');
		//			// выводим надпись из метки 'label2', из файла локализации плагина
		//			$args['blocks']['about']['content'] = $this->gettext('label2');
		//			// выводим надпись "Основные настройки", из основного файла локализации приложения
		//			$args['blocks']['main']['name'] = $this->gettext('mainoptions');
		//			// создадим два поля для ввода номеров: первое письмо и второе письмо
		//			// 'name' => 'имя_тега_html', 'id' => $teg_id, 'size' => размер поля (ширина)
		//			$inputfld_start = new html_inputfield(array('name'=> 'first_letter','id'  => $teg_id,'size'=> 5));
		//			$inputfld_end = new html_inputfield(array('name'=> 'second_letter','id'  => $teg_id,'size'=> 5));
		//			// добавим контент для пояснения в виде таблицы
		//			$table = new html_table();
		//
		//			/// Блок "Основные настройки". Начало
		//			$storage = $this->rc->get_storage();
		//			$folder = $storage->get_folder();
		//			$lst_msg = $storage->list_messages($folder, null, null, 'ASC', null);
		//			// получаем номер первого письма
		//			//$msg_start =
		//			$a = 1;
		//			// получаем номер второго письма
		//			//$msg_end =
		//
		//			// выводим номер первого письма
		//			$args['blocks']['main']['options']['first_letter'] = array(
		//				// выводим надпись из метки 'label3', из файла локализации плагина
		//				'title'=> $this->gettext('label3'),
		//				// текстовое поле с номером первого письма
		//				'content'=> $inputfld_start->show(1)
		//			);
		//			// выводим номер второго письма
		//			$args['blocks']['main']['options']['second_letter'] = array(
		//				// выводим надпись из метки 'label4', из файла локализации плагина
		//				'title'=> $this->gettext('label4'),
		//				// текстовое поле с номером второго письма
		//				'content'=> $inputfld_end->show(2)
		//			);
		//			// надпись с пояснением где начало списка и где конец - надпись из метки 'label8', из файла локализации плагина
		//			$args['blocks']['main']['options']['table'] = array('content'=> $this->gettext('label8'));
		//
		//
		//
		//			//		// Блок "Основные настройки". Конец
		//			//		// Блок "Выводная информация о работе плагина". Начало
		//			//		// Выводим надпись из метки 'label5', из файла локализации плагина
		//			////		$args['blocks']['letters']['name'] = $this->gettext('label5');
		//			//		// номер первого письма
		//			////		$args['blocks']['letters']['options']['first_current_letter'] = array(
		//			////			// выводим надпись из метки 'label3', из файла локализации плагина
		//			////			'title'=> $this->gettext('label3'),
		//			////			// текстовое поле с номером первого письма
		//			////			'content'=> 123 // "надпись с номером первого письма" //$this->gettext('')
		//			////		);
		//			//		// номер второго письма
		//			////		$args['blocks']['letters']['options']['second_current_letter'] = array(
		//			////			// выводим надпись из метки 'label4', из файла локализации плагина
		//			////			'title'=> $this->gettext('label4'),
		//			////			// текстовое поле с номером второго письма
		//			////			'content'=> 456 // "надпись с номером второго письма" //$this->gettext('')
		//			////		);
		//			////		foreach ($list_folder as  $val) {
		//			////			echo  $val;  // выведет 123
		//			////		}
		//			//		// Выводим надпись из метки 'label5', из файла локализации плагина
		//			////		$args['blocks']['list_folders']['name'] = "Список папок"; // $this->gettext('label5');
		//			//		// список папок $list_folders
		//			////		$args['blocks']['list_folders']['options'] = array(
		//			////			// выводим надпись из метки 'label4', из файла локализации плагина
		//			////			'title'=> "Список папок",
		//			////			// список папок
		//			////			'content'=> 1
		//			////		);
		//		}
		//		// Блок "Основные настройки". Конец
		//		return $args;
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
}