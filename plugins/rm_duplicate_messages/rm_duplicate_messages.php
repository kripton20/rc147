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
        // Переменная $this относится к текущему классу и представляет собой неявный объект.
        // rc - свойство этого объекта. Запишем туда системные настройки приложения.
        $this->rc = rcmail::get_instance();
        // переменной $tsk присвоим имя текущей задачи приложения
        //$tsk = $this->rc->task;
        // если задача 'mail' и действие '' или 'list', покажем нашу кнопку на панели, в других случаях не показываем
        if ($this->rc->task == 'mail' && ($this->rc->action == '' || $this->rc->action == 'list')) {
            /**
            * Регистрируем хуки сервера
            * Способ работы хуков плагинов заключается в том, что в разное время, пока Roundcube обрабатывает, он проверяет,
            * есть-ли у каких-либо плагинов зарегистрированные функции для запуска в это время, и если да, то функции запускаются
            * (путем выполнения «ловушки»). Эти функции могут изменять или расширять поведение Roundcube по умолчанию.
            * Регистрация хуков:     $this->add_hook('hook_name', $callback_function);
            * где второй аргумент – это обратный вызов PHP (функция в этом файле ниже), который может ссылаться на простую функцию или метод
            * объекта. Зарегистрированная функция получает один хеш-массив в качестве аргумента, который содержит определенные данные текущего
            * контекста в зависимости от ловушки.
            * См. «Перехватчики подключаемых модулей» для получения полного описания всех перехватчиков и их полей аргументов.
            * Аргумент var может быть изменен функцией обратного вызова и может (даже частично) быть возвращен приложению.
            * Список хуков содержится в $this->$api->$handlers
            */
            /**
            * preferences_save
            * Позволяет плагину вводить данные в массив пользовательских настроек, которые будут сохранены
            * Аргументы:
            * @param prefs: Хеш-массив с сохраняемыми prefs
            * Возвращаемые значения:
            * @return result: логический
            * @return abort: логическое
            * @return prefs: массив
            */
            // Если зарегистрировать хук здесь - то он будет работать при срабатывании
            // функции 'init' (при каждой перезагрузке страницы) с учетом значений 'task' и 'action'.
            // Срабатывание функции 'save_settings' при каждой перезагрузке страницы не требуется.
            $this->add_hook('preferences_save', array($this,'save_settings'));
            /**
            * preferences_update
            * В отличие от хука preferences_save, он запускается всякий раз, когда пользовательские настрой-ки обновляются. И это не ограничивается разделом настроек, но также может выполняться другим плагином.
            * Аргументы:
            * @param prefs: хеш-массив с префиксом, который нужно обновить
            * @param old: массив хешей с текущими сохраненными пользовательскими настройками
            * @param userid: ID пользователя, для которого сохраняются эти настройки.
            * Возвращаемые значения:
            * @return prefs: массив
            * @return old: массив
            * @return abort: логическое
            */
            // Срабатывание функции 'update_settings' требуется при каждой перезагрузке страницы,
            // поэтому зарегистрируем хук с этой функцией здесь.
            $this->add_hook('preferences_update', array($this,'update_settings'));
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
            // Добавим наши локализованные метки на страницу настроек плагина
            $this->add_texts('localization', array(
                    'lbl1',
                    'lbl2',
                    'lbl3',
                    'lbl4',
                    'lbl5',
                    'lbl6',
                    'lbl7',
                    'lbl8',
                    'lbl9',
                    'lbl10',
                    'lbl11',
                    'lbl_msg_request',
                    'lbl_get_msg',
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
                    'label'=> 'lbl1',// локализованная надпись на кнопке
                    'title'=> 'lbl2',// локализованная всплывающая подсказка
                    'command'=> 'plugin.btn_cmd_msg_request',// имя выполняемой команды для кнопки
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
            //$this->register_action('plugin.msg_request', array($this,'msg_request'));
            $this->register_action('plugin.msg_request', array($this,'msg_request'));
            $this->register_action('save-prefs', array($this,'msg_request'));
        }elseif ($this->rc->task == 'settings') {

			// загружаем файл скина плагина
			//$this->includeCSS();

			/**
			* Регистрируем хуки сервера
			* Способ работы хуков плагинов заключается в том, что в разное время, пока Roundcube обрабатывает, он проверяет,
			* есть-ли у каких-либо плагинов зарегистрированные функции для запуска в это время, и если да, то функции запускаются
			* (путем выполнения «ловушки»). Эти функции могут изменять или расширять поведение Roundcube по умолчанию.
			* Регистрация хуков:     $this->add_hook('hook_name', $callback_function);
			* где второй аргумент – это обратный вызов PHP (функция в этом файле ниже), который может ссылаться на простую функцию или метод
			* объекта. Зарегистрированная функция получает один хеш-массив в качестве аргумента, который содержит определенные данные текущего
			* контекста в зависимости от ловушки.
			* См. «Перехватчики подключаемых модулей» для получения полного описания всех перехватчиков и их полей аргументов.
			* Аргумент var может быть изменен функцией обратного вызова и может (даже частично) быть возвращен приложению.
			*/
			$this->add_hook('preferences_sections_list', array($this,'insert_section'));
			$this->add_hook('preferences_list', array($this,'settings_blocks'));
			$this->add_hook('preferences_save', array($this,'save_settings'));
			//$this->add_hook('folder_form', array($this,'folder_form'));
			$this->add_hook('preferences_update', array($this,'update_settings'));

		}
        // когда наша функция запускается - страница обновляется, функцию обратного вызова требуется зарегистрировать еще раз
        elseif ($this->rc->action == 'plugin.msg_request') {
            $this->register_action('plugin.msg_request', array($this,'msg_request'));
            // Определим хуки которые будут работать в контексте функции 'msg_request'.
            $this->add_hook('preferences_save', array($this,'save_settings'));
            // Срабатывание функции 'update_settings' требуется при каждой перезагрузке страницы.
            $this->add_hook('preferences_update', array($this,'update_settings'));
        }
    }

    // Функция запрашивает очередное сообщение из базы и передаёт в клиентскую часть.
    function msg_request()
    {
        // из глобального массива 'POST' получаем 'uids' выделенных сообщений
        //$uids = rcmail::get_uids(null, null, $multifolder, rcube_utils::INPUT_POST);

        // из глобального массива 'POST' получаем имя текущей папки '_mbox'
        //$folder = rcube_utils::get_input_value('_mbox', rcube_utils::INPUT_POST);
        $folder = $_POST['_mbox'];

        // Преобразуем двумерный массив 'uids' в одномерный массив 'uids'.
        $uids   = $uids[$folder];

        // Создадим наши записи в массиве пользовательских настроек 'prefs'.
        // Сохраним туда массив 'uids' и имя текущей папки 'folder'.
        $user_prefs['rm_duplicate_messages'] = array(
            'uids'  =>'uids1',//$uids,
            'folder'=>'folder1' //$folder
        );
//$this->rc->task = 'login|logout|mail|settings';
//$this->task = 'login|logout|mail|settings';

        // Вызываем хук с функцией сохранения настроек в массиве пользовательских настроек 'prefs'.
//        $this->rc->plugins->exec_hook('preferences_save', array(
//                // Передаём название нашего плагина в качестве имени секции в массиве пользовательских настроек 'prefs'.
//                'section'=> 'rm_duplicate_messages')
//        );

//$plugin = rcmail::get_instance()->plugins->exec_hook('preferences_save',
//    array('prefs' => $user_prefs, 'section' => 'rm_duplicate_messages'));

$a=rcube_user::save_prefs($a_user_prefs);

        // Создаём экземпляр класса    'rcube_user' и передаём ему ID текущего пользователя.
        $rcu            = new rcube_user($this->rc->user->ID);

        // Получаем текущие предпочтения текущего пользователя.
        $old_user_prefs = $rcu->get_prefs();

        // Вызываем хук  и передаём параметры:
        // Вызываем хук 'preferences_update' с функцией обновления настроек в массиве пользовательских настроек 'prefs'.
        $this->rc->plugins->exec_hook('preferences_update', array(
                // ID текущего пользователя.
                'userid'=> $this->rc->user->ID,//$this->ID,
                // Предпочтения текущего пользователя которые нужно изменить.
                'prefs'=> $user_prefs,// $user_prefs, //$a_user_prefs,
                // Старые предпочтения текущего пользователя которые есть в хранилище.
                'old'=> $old_user_prefs
            ));

        /**
        * Инициализация и получение объекта хранения писем
        *
        * @return rcube_storage Storage        Объект хранения
        */
        $storage = $this->rc->get_storage();
        // Получаем из хранилища наши данные.
        //$cfg = $this->rc->config->get('rm_duplicate_messages');

    }

    // Функция получает список uids из массива $_POST и сохранияет конфигурацию.
    function save_settings($args)
    {
if ($args['section'] == 'rm_duplicate_messages') {// Должна быть save-prefs
	        // Из глобального массива 'POST' получаем 'uids' выделенных сообщений.
        //$uids = rcmail::get_uids(null, null, $multifolder, rcube_utils::INPUT_POST);

        // Из глобального массива 'POST' получаем имя текущей папки '_mbox'
        $folder = $_POST['_mbox'];

        // Преобразуем двумерный массив 'uids' в одномерный массив 'uids'.
        $uids   = $uids[$folder];

        // Создадим наши записи в массиве пользовательских настроек 'prefs'.
        // Сохраним туда массив 'uids' и имя текущей папки 'folder'.
        $args['prefs']['rm_duplicate_messages'] = array(
            'uids'  =>'uids2',//$uids,
            'folder'=>'folder2' //$folder
        );
                // Удалим ранее созданные наши записи в массиве пользовательских настроек 'prefs'.
        //$args['prefs']['rm_duplicate_messages'] = NULL;
        //$this->update_settings ($args);
		}

        return $args;
    }

    function update_settings ($args)
    {
        //        // $this->rc->action == '' || $this->rc->action == 'list'
        //        //if ($_POST['_action'] == 'save - prefs') {
        //        //$dolds = $args['old']['rm_duplicate_messages'];
        //        // Обновим наши записи в массиве пользовательских настроек 'prefs'.
        //        //        $args['prefs']['rm_duplicate_messages'] = array(
        //        //            'uids'  =>'uid3',
        //        //            'folder'=>'INBOX3');
        //        // из глобального массива 'POST' получаем 'uids' выделенных сообщений
        //        $uids = rcmail::get_uids(null, null, $multifolder, rcube_utils::INPUT_POST);
        //        // из глобального массива 'POST' получаем имя текущей папки '_mbox'
        //        //$folder1 = rcube_utils::get_input_value('_mbox', rcube_utils::INPUT_POST);
        //        $folder = $_POST['_mbox'];
        //        // Преобразуем двумерный массив 'uids' в одномерный массив 'uids'.
        //        $uids = $uids[$folder];
        //        // Создадим наши записи в массиве пользовательских настроек 'prefs'.
        //        // Сохраним туда массив 'uids' и имя текущей папки 'folder'.
        //        $args['prefs']['rm_duplicate_messages'] = array(
        //            'uids'  => 'uids', //$uids,
        //            'folder'=>'folder' //$folder
        //            );

        // Если в массиве '$_REQUEST' есть наша акция - выполняем инструкции перезаписи
        // данные - считаем их от туда, если нет перезапишем имеющиеся
        // Акции которые перезаписывают: list, save-prefs
        //if ($_POST['_action'] == 'loading1612877025319') {plugin.msg_request
        // Для работы этого блока и сохранения параметров нужно вызвать акцию save-prefs.
        // И регистрировать её в своём плагине не нужно.
       if ($_REQUEST['_action'] == 'save-prefs') {
        // Из глобального массива 'POST' получаем имя текущей папки '_mbox'
        $folder = $_POST['_mbox'];

        // Из глобального массива 'POST' получаем 'uids' выделенных сообщений.
        //$uids = rcmail::get_uids(null, null, $multifolder, rcube_utils::INPUT_POST);

        // Преобразуем двумерный массив 'uids' в одномерный массив 'uids'.
        $uids   = $uids[$folder];
        //$args['prefs']['rm_duplicate_messages'] = 
        // Создадим наши записи в массиве пользовательских настроек 'prefs'.
        // Сохраним туда массив 'uids' и имя текущей папки 'folder'.
        $args['prefs']['rm_duplicate_messages'] = array(
            'uids'  =>'uids12',//$uids,
            'folder'=>'folder12' //$folder
        );
        //}else {//вот этат часть записывает
//            if (isset($args['old']['rm_duplicate_messages'])) {
//                //$old_args = [];
//                //$args['prefs']['rm_duplicate_messages'] = $args['old']['rm_duplicate_messages'];
//                $args['prefs']['rm_duplicate_messages'] = array(
//            'uids'  =>'uids9',//$uids,
//            'folder'=>'folder9' //$folder
//        );
            //}
       }
                // Удалим ранее созданные наши записи в массиве пользовательских настроек 'prefs'.
                //$args['prefs']['rm_duplicate_messages'] = NULL;
                
// Вернём полученное значение в вызывающую функцию.
        return $args;
    }

// Вставим название нашей секции
	// Получите локализованный текст на желаемом языке
	// Обертка для rcube::gettext() с добавлением ID плагина в качестве домена
	function insert_section ($args)
	{
		//$this->logger('psections ', $args);
		$args['list']['rm_duplicate_messages'] = array(
			'id'     =>'rm_duplicate_messages',
			'section'=>'rm_duplicate_messages');
		return $args;
	}

	// Блок обработки настроек плагина (выпадающий список)
	function settings_blocks ($args)
	{
		//$this->logger('prefslist ', $args);		//return $args;
		if ($args['section'] == 'rm_duplicate_messages') {

			// Обертка для rcube::gettext() с добавлением ID плагина в качестве домена
			// функция $this->gettext('параметр_из_общего_массива_локализации')
			$args['blocks']['blurb']['name'] = $this->gettext('about');
			$args['blocks']['blurb']['content'] = $this->gettext('blurbcontent');
			$args['blocks']['main']['name'] = $this->gettext('mainoptions');
			$args['blocks']['main']['options']['rm_duplicate_messages'] = array(

				'uids'  =>'uids2',//$uids,
            'folder'=>'folder2' //$folder
			);
			
		}
		return $args;
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