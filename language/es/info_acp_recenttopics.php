<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2015 PayBas
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Based on the original NV Recent Topics by Joas Schilling (nickvergessen)
 * Spanish translation by Raul [ThE KuKa] (www.phpbb-es.com)
 */

if (!defined('IN_PHPBB'))
{
	exit;
}
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge(
	$lang, array(
	// PCA foro
	'RECENT_TOPICS_LIST'            => 'Mostrar en “Temas Recientes”',
	'RECENT_TOPICS_LIST_EXPLAIN'    => 'Activar para mostrar los temas de este foro en “Temas Recientes”',

	//PCA título
	'RECENT_TOPICS'                 => 'Temas Recientes',
	'RT_CONFIG'                     => 'Configuración',
	'RECENT_TOPICS_EXPLAIN'         => 'En esta página puede cambiar las opciones especificas para la extensión “Temas Recientes”.<br /><br />Foros específicos pueden ser incluidos o excluidos editando los respectivos foros en el PCA.<br />Asegúrese también de comprobar los permisos de sus usuarios, los cuales permiten a los usuarios cambiar individualmente algunas de las opciones encontradas abajo.',

	//ajustes globales
	'RT_GLOBAL_SETTINGS'            => 'Opciones globales',
	'RT_DISPLAY_INDEX'              => 'Mostrar en el índice',
	'RT_DISPLAY_VIEWFORUM'          => 'Mostrar en la página del foro',
	'RT_VIEWFORUM_LOCATION'         => 'Ubicación en vista del foro',
	'RT_VIEWFORUM_LOCATION_EXP'     => 'Seleccione dónde mostrar los temas recientes en la página del foro. Esta configuración es independiente de la página del índice.',
	'RT_NUMBER'                     => 'Temas Recientes',
	'RT_NUMBER_EXP'                 => 'Número de temas a mostrar.',
	'RT_PAGE_NUMBER'                => 'Páginas de temas recientes',
	'RT_PAGE_NUMBER_EXP'            => 'Esta función sobrescribe el número máximo de páginas establecido y muestra todas las páginas sin importar cuántas páginas haya establecido la opción.',
	'RT_PAGE_NUMBERMAX'		=> 'Número máximo de páginas',
	'RT_PAGE_NUMBERMAX_EXP'		=> 'Definir el número máximo de páginas',
	'RT_MIN_TOPIC_LEVEL'            => 'Nivel de tema mínimo',
	'RT_MIN_TOPIC_LEVEL_EXP'        => 'Determina el nivel de tema mínimo para poder ser mostrado. Solo mostrará temas del nivel especificado y superior.',
	'RT_ANNOUNCEMENTS_FIRST'        => 'Mostrar primero los anuncios',
	'RT_ANNOUNCEMENTS_FIRST_EXP'    => 'Coloca los anuncios y anuncios globales de cada página de la lista al principio de esa página. Los anuncios más antiguos que no estén en la página no se añaden.',
	'RT_ANTI_TOPICS'                => 'Temas excluidos',
	'RT_ANTI_TOPICS_EXP'            => 'Las IDs de los temas a excluir, separados por "," (Por ejemplo: 7,9)<br />Si no quiere excluir un tema, simplemente introduzca 0.',
	'RT_PARENTS'                    => 'Mostrar foros padre',
	'RT_PARENTS_EXP'                => 'Mostrar foros padre dentro de la fila del tema de "Temas Recientes".',
	'RT_TOPIC_LINK_TO'              => 'El título del tema enlaza a',
	'RT_TOPIC_LINK_TO_EXP'          => 'Elija a qué mensaje enlaza el título del tema en la lista de Temas Recientes.',
	'RT_TOPIC_LINK_FIRST'           => 'Primer mensaje',
	'RT_TOPIC_LINK_LAST'            => 'Último mensaje',
	'RT_TOPIC_LINK_UNREAD'          => 'Primer mensaje no leído',
	'RT_SIDE_SHOW_DATE'             => 'Mostrar fecha en vista lateral',
	'RT_SIDE_SHOW_DATE_EXP'         => 'Muestra la fecha del mensaje en la vista lateral. Desactiva para una barra lateral más compacta.',
	'RT_SHOW_LIKES'                 => 'Mostrar cantidad de me gusta',
	'RT_SHOW_LIKES_EXP'             => 'Muestra la cantidad de me gusta de PostLove junto a los temas recientes.',

	// Opciones modificables por el usuario. Afectan a los usuarios anónimos y pueden ser sobreescritas por el PCU
	'RT_OVERRIDABLE'                => 'Opciones sobreescribibles del PCU',
	'RT_LOCATION'                   => 'Posición',
	'RT_LOCATION_EXP'               => 'Elija un lugar para mostrar la lista de temas recientes.',
	'RT_TOP'                        => 'Mostrar en la parte superior',
	'RT_BOTTOM'                     => 'Mostrar en la parte inferior',
	'RT_SIDE'                       => 'Mostrar en el lado derecho',
	'RT_SORT_START_TIME'            => 'Ordenar temas por la hora de inicio',
	'RT_SORT_START_TIME_EXP'        => 'Habilitar para ordenar la lista de temas recientes en base a la hora de inicio del tema, en lugar de la de la última respuesta.',
	'RT_UNREAD_ONLY'                => 'Mostrar solo temas no leídos',
	'RT_UNREAD_ONLY_EXP'            => 'Activar para mostrar solo temas no leídos (tanto si son “recientes” o no). Esta función utiliza la misma configuración (excluyendo foros, temas, etc.) que el modo normal. Nota: esto sólo funciona para usuarios identificados; los invitados verán la lista normal.',
	'RT_RESET_DEFAULT'              => 'Reiniciar la configuración de los usuarios',
	'RT_RESET_DEFAULT_EXP'          => 'Devuelve la configuración independiente de cada usuario de “Temas Recientes” al valor por defecto.',

	//Version checker
	'RT_VERSION_CHECK'				=> 'Comprobación de la versión',
	'RT_LATEST_VERSION'				=> 'Última versión',
	'RT_EXT_VERSION'				=> 'Versión de la extensión',
	'RT_CHECK_UPDATE'				=> 'Visita <a href="https://www.avathar.be/forum/app.php/dlext/details?df_id=35">avathar.be</a> para comprobar si hay actualizaciones disponibles.',

	//Standalone pages
	'RT_PAGES'                      => 'Páginas independientes',
	'RT_PAGE_ENABLE'                => 'Activar páginas independientes',
	'RT_PAGE_ENABLE_EXP'            => 'Permitir el acceso a las páginas independientes de temas recientes. Esto es independiente de la visualización en la página de índice.',
	'RT_PAGE'                       => 'Página completa',
	'RT_PAGE_EXP'                   => 'Página independiente de Temas Recientes con encabezado y pie de página completos del foro.',
	'RT_SIMPLE_PAGE'                => 'Página simplificada',
	'RT_SIMPLE_PAGE_EXP'            => 'Página simplificada de Temas Recientes sin encabezado ni pie de página del foro, adecuada para incrustar en un iframe.',
	'RT_VIEW_PAGE'                  => 'Ver página en nueva pestaña',

	//Bloque publicitario
	'RT_ADS_SETTINGS'           => 'Bloque publicitario',
	'RT_ADS_ENABLE'             => 'Activar bloque publicitario',
	'RT_ADS_ENABLE_EXP'         => 'Muestra un bloque HTML personalizado junto a los temas recientes en la página de inicio. Solo visible cuando la ubicación está configurada en "Lateral".',
	'RT_ADS_CODE'               => 'HTML publicitario',
	'RT_ADS_CODE_EXP'           => 'Introduzca HTML personalizado para mostrar en el bloque publicitario (por ejemplo, código de anuncios, botón de donación u otro contenido).',

	//Donation
	'PATREON_ALT'                => 'Hazte mecenas',
	'RT_DONATE'					=> 'Donar a RecentTopics',
	'RT_DONATE_SHORT'			=> 'Haga una donación a RecentTopics',
	'RT_DONATE_EXPLAIN'			=> 'RecentTopics es 100% gratis. Es un proyecto que hago en mi tiempo libre donde invierto mi tiempo y dinero por gusto. Si disfruta utilizando RecentTopics, por favor considere hacer una donación. Sin ataduras.',
	)
);
