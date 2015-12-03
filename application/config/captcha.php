<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @package  Captcha
 *
 * Captcha configuration is defined in groups which allows you to easily switch
 * between different Captcha settings for different forms on your website.
 * Note: all groups inherit and overwrite the default group.
 *
 * Group Options:
 *  type		Captcha type, e.g. basic, alpha, word, math, riddle
 *  width		Width of the Captcha image
 *  height		Height of the Captcha image
 *  complexity	Difficulty level (0-10), usage depends on chosen style
 *  background	Path to background image file
 *  fontpath	Path to font folder
 *  fonts		Font files
 *  promote		Valid response count threshold to promote user (FALSE to disable)
 */

return array(
	'default' => array(
		'style'      	=> 'word',
		'width'      	=> 320,
		'height'     	=> 60,
		'complexity' 	=> 4,
		'background' 	=> '',
		'fontpath'   	=> MODPATH.'captcha/fonts/',
		'fonts'      	=> array('DejaVuSerif.ttf'),
		'promote'    	=> FALSE,
	),
	// Words of varying length for Captcha_Word to pick from
	// Note: all Unicode characters should work, but not everyone can type them, so be careful with that (no japanese/chinese captcha please ;))
	'words' => array
	(
		'да', 'нет', 'он', 'оно', 'они', 'его','солнце','лето','зима','весна','осень','крым','ярмарка',
		"ад", "аж", "аз", "аи", "ай", "ан", "ар", "ас", "ау", "ах", "ба", "бы", "во", "вы", "га", "гм", "да", "до", "ее", "еж", "ер", "же", "за", "из", "ил", "ин", "их", "ка", "ко", "кш", "ли", "ль", "ля", "ми", "мм", "мо", "му", "мы", "на", "не", "ни", "но", "ну", "ню", "об", "ой", "ом", "он", "оп", "от", "ох", "па", "по", "ре", "се", "си", "со", "су", "то", "ты", "уа", "уд", "уж", "ук", "ум", "ус", "уф", "ух", "фа", "фи", "фу", "ха", "хм", "це", "че", "чу", "ша", "ща", "щи", "эй", "эк", "эм", "эн", "эр", "эс", "эф", "эх", "юг", "юз", "юр", "юс", "ют", "яд", "яз", "як", "ял", "ям", "яр", "яс", "аба", "абы", "ага", "агу", "азу", "азы", "аил", "аир", "акр", "акт", "али", "аль", "ант", "ара", "асс", "ату", "аул", "аут", "ахи", "баз", "бай", "бак", "бал", "бар", "бас", "бах", "бац", "баш", "бег", "беж", "без", "бей", "бек", "бел", "бес", "бис", "бит", "бич", "боа", "боб", "бог", "бой", "бок", "бом", "бон", "бор", "бот", "бош", "бра", "бри", "брр", "буж", "буй", "бук", "бум", "бур", "бут", "бух", "бык", "быт", "бэр", "важ", "вал", "вар", "ваш", "век", "вес", "виг", "вид", "вир", "вис", "вне", "вод", "воз", "вой", "вол", "вон", "вор", "вот", "все", "вуз", "вша", "выя", "вяз", "гад", "газ", "гай", "гак", "гам", "где", "гей", "ген", "гид", "гик", "гит", "гну", "год", "гой", "гол", "гон", "гоп", "гот", "гуд", "гуж", "гук", "гул", "дар", "два", "дед", "дек", "дер", "див", "для", "дно", "дог", "дож", "док", "дол", "дом", "дон", "дот", "дуб", "дух", "душ", "дым", "его", "еда", "ежа", "еле", "ель", "ера", "ерш", "еры", "ерь", "еще", "жар", "жид", "жим", "жир", "жок", "жом", "жор", "жох", "жук", "зав", "зад", "зал", "зам", "зги", "зев", "зет", "зиг", "зло", "зоб", "зов", "зря", "зуб", "зуд", "зуй", "зык", "ибо", "ива", "иго", "идо", "иды", "иже", "изо", "икс", "или", "иль", "имя", "инк", "иол", "ион", "иск", "ишь", "йог", "йод", "йот", "как", "кал", "кап", "кат", "кеб", "кед", "кий", "кик", "кил", "кит", "код", "кой", "кок", "кол", "ком", "кон", "кот", "кош", "кто", "куб", "кум", "кун", "кур", "кус", "кут", "куш", "лаг", "лад", "лаж", "лаз", "лай", "лак", "лал", "лан", "лар", "лев", "лед", "леи", "лей", "лек", "лен", "лес", "лет", "лещ", "лея", "лик", "лис", "лиф", "лоб", "лов", "лог", "лом", "лот", "лох", "луб", "луг", "лук", "луч", "лье", "люб", "люд", "люк", "ляд", "лях", "маг", "маз", "май", "мак", "мат", "мах", "мга", "мед", "меж", "мел", "мех", "меч", "миг", "мим", "мир", "миф", "мой", "мол", "мор", "мот", "мох", "муж", "мул", "мыс", "мыт", "мэр", "мяу", "мяч", "над", "нар", "наш", "нет", "неф", "низ", "ниц", "нож", "нок", "ном", "нос", "нут", "нэп", "нюх", "оба", "обо", "ого", "ода", "одр", "око", "она", "они", "оно", "опт", "орс", "орт", "оса", "ост", "ось", "ото", "охи", "очи", "паж", "паз", "пай", "пак", "пал", "пан", "пар", "пас", "пат", "паф", "пах", "пек", "пес", "пик", "пим", "пир", "пли", "под", "пол", "поп", "пот", "при", "про", "пря", "пуд", "пук", "пул", "пуп", "пуф", "пух", "пыж", "пыл", "пых", "пэр", "раб", "рад", "раж", "раз", "рай", "рак", "рев", "рез", "рей", "рея", "ржа", "рис", "риф", "ров", "рог", "род", "рой", "рок", "рол", "ром", "рот", "рцы", "рык", "рым", "рюш", "ряд", "ряж", "сад", "саж", "саз", "сак", "сам", "сан", "сап", "сев", "сей", "сем", "сет", "сиг", "сие", "сип", "сок", "сом", "сон", "сор", "сот", "соя", "сто", "суд", "сук", "суп", "сын", "сыр", "сыч", "сэр", "сяк", "сям", "таз", "таи", "так", "тал", "там", "тат", "тем", "тес", "тик", "тип", "тир", "тис", "тиф", "тля", "той", "ток", "тол", "том", "тон", "топ", "тор", "тот", "три", "тсс", "туз", "тук", "тун", "тур", "тут", "туф", "туш", "туя", "тык", "тыл", "тын", "тюк", "тяж", "тяп", "увы", "угу", "уда", "удэ", "уже", "ужо", "уза", "узы", "унт", "ура", "усы", "уха", "ухо", "уши", "уют", "фаг", "фай", "фал", "фас", "фат", "фен", "фес", "фея", "фок", "фол", "фон", "фот", "фри", "фру", "фря", "фуй", "фук", "фут", "хаз", "хай", "хам", "хан", "хап", "хек", "хна", "хны", "ход", "хон", "хоп", "хор", "хук", "цап", "цеж", "цеп", "цех", "цок", "цоп", "цуг", "цук", "цыц", "чад", "чай", "чал", "чан", "час", "чей", "чек", "чем", "чес", "чет", "чех", "чиж", "чий", "чик", "чин", "чих", "чон", "чох", "что", "чуб", "чум", "чур", "шаг", "шар", "шах", "шед", "шеф", "шея", "шик", "шип", "шиш", "шов", "шок", "шум", "шут", "щец", "щип", "щит", "щуп", "щур", "эва", "эге", "эка", "экю", "эль", "эму", "эра", "эрг", "эре", "эрл", "эст", "это", "эфа", "эхо", "юит", "юла", "юра", "юрк", "юрт", "явь", "яга", "язь", "яко", "яма", "ямб", "ярд", "ярл", "ярь", "ять",
		"Агрессия", "Агрономия", "Алиби", "Алкоголь", "Алфавит", "Аналог", "Анатом", "Анестезия", "Аноним", "Антитеза", "Апартеид", "Апостроф", "Асбест", "Атлет", "Афера", "Баловать", "Банты", "Баржа", "Боязнь", "Бредовый", "Броня", "Брошюра", "Бряцать", "Бытие", "Вандалы", "Ваяние", "Векселя", "Верба", "Вестерн", "Вечеря", "Возраст", "Вор", "Ворожея", "вред", "Всплеск", "Выборы", "Гаер", "Гантель", "Генезис", "Герб", "Гравёр", "Грамм". "Граффити", "Гренадер", "Гренки", "Гротеск", "Гусь", "Дебош", "Декольте", "Демарш", "Демпинг", "Деньги", "Дерматин", "Деспотия", "Детектив", "Дефис", "Диалог", "Диоптрия", "Диптих", "Дисплей", "Добела", "Добыча", "Догмат", "Договор", "Допьяна", "Досуг", "Дремота", "Дочиста", "Духовник", "Дуршлаг", "Еретик", "Жалюзи", "Жёлоб", "Желчь", "Жерло", "Житие", "Завидно", "Заводской", "Заговор", "Задолго", "Заём", "Закуток", " Занятой", " Засуха", "Зевота", "Злоба", "Знамение", "Знахарка", "Зонт",
	),
	// Riddles for Captcha_Riddle to pick from
	// Note: use only alphanumeric characters
	'riddles' => array
	(
		array('Do you hate spam? (yes or no)', 'yes'),
		array('Вы робот? (да или нет)', 'нет'),
		array('Fire is... (hot or cold)', 'hot'),
		array('The season after fall is...', 'winter'),
		array('Which day of the week is it today?', strftime('%A')),
		array('Which month of the year are we in?', strftime('%B')),
	),
);