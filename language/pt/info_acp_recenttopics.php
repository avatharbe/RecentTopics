<?php
/**
 *
 * @package Recent Topics Extension
 * @copyright (c) 2015 PayBas
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Based on the original NV Recent Topics by Joas Schilling (nickvergessen)
 * Portuguese translation by phpbbpt
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
	//ACP Fórum
	'RECENT_TOPICS_LIST'            => 'Exibir tópicos recentes',
	'RECENT_TOPICS_LIST_EXPLAIN'    => 'Ativar para exibir tópicos neste fórum na extensão "tópicos recentes"',

	//PCA título
	'RECENT_TOPICS'                 => 'Tópicos recentes',
	'RT_CONFIG'                     => 'Configuração',
	'RECENT_TOPICS_EXPLAIN'         => 'Nesta página você pode alterar as configurações específicas para a extensão Recent Topics. <br /> <br /> Fóruns específicos podem ser incluídos ou excluídos editando os respectivos fóruns em seu ACP. Certifique-se de verificar as permissões de usuário, que permitem aos usuários alterar algumas das configurações encontradas abaixo para si.',

	//configurações globais
	'RT_GLOBAL_SETTINGS'            => 'Configurações globais',
	'RT_DISPLAY_INDEX'              => 'Exibir na página de índice',
	'RT_DISPLAY_VIEWFORUM'          => 'Exibir na página do fórum',
	'RT_VIEWFORUM_LOCATION'         => 'Local de exibição no fórum',
	'RT_VIEWFORUM_LOCATION_EXP'     => 'Selecione onde exibir os tópicos recentes na página do fórum. Esta configuração é independente da página de índice.',
	'RT_NUMBER'                     => 'Número de tópicos recentes para mostrar',
	'RT_NUMBER_EXP'                 => 'Número máximo de tópicos a serem exibidos por página.',
	'RT_PAGE_NUMBER'                => 'Mostrar todas as páginas de tópicos recentes',
	'RT_PAGE_NUMBER_EXP'            => 'Esta função substitui o número máximo definido de páginas e mostra todas as páginas, independentemente de quantas páginas são definidas pela opção.',
	'RT_PAGE_NUMBERMAX'             => 'Número máximo de páginas',
	'RT_PAGE_NUMBERMAX_EXP'         => 'Defina o máximo da página para exibir na paginação dos tópicos recentes, a menos que seja substituído.',
	'RT_MIN_TOPIC_LEVEL'            => 'Nível mínimo do tipo de tópico',
	'RT_MIN_TOPIC_LEVEL_EXP'        => 'Determina o nível mínimo do tipo de tópico a ser exibido. Ele só exibirá tópicos do nível definido e mais alto.',
	'RT_ANNOUNCEMENTS_FIRST'        => 'Mostrar anúncios primeiro',
	'RT_ANNOUNCEMENTS_FIRST_EXP'    => 'Coloca os anúncios e anúncios globais de cada página da lista no topo dessa página. Anúncios mais antigos que não estejam na página não são adicionados.',
	'RT_ANTI_TOPICS'                => 'ID de tópico excluído',
	'RT_ANTI_TOPICS_EXP'            => 'Os IDs de tópicos a excluir, separados por "," (Exemplo: 7,9) <br />O valor 0 desabilita esse comportamento.',
	'RT_ANTI_TOPICS_INVALID'        => 'Os IDs dos tópicos excluídos devem ser números inteiros separados por vírgulas, por exemplo 7,9. Nada foi guardado.',
	'RT_PARENTS'                    => 'Mostrar Fórum Pai',
	'RT_PARENTS_EXP'                => 'Exibir fóruns pai dentro da linha tópico de tópicos recentes.',
	'RT_TOPIC_LINK_TO'              => 'Título do tópico liga para',
	'RT_TOPIC_LINK_TO_EXP'          => 'Escolha para qual mensagem o título do tópico liga na lista de Tópicos Recentes.',
	'RT_TOPIC_LINK_FIRST'           => 'Primeira mensagem',
	'RT_TOPIC_LINK_LAST'            => 'Última mensagem',
	'RT_TOPIC_LINK_UNREAD'          => 'Primeira mensagem não lida',
	'RT_SIDE_SHOW_DATE'             => 'Mostrar data na vista lateral',
	'RT_SIDE_SHOW_DATE_EXP'         => 'Exibe a data da mensagem na vista lateral. Desative para uma barra lateral mais compacta.',
	'RT_SHOW_LIKES'                 => 'Mostrar contagem de gostos',
	'RT_SHOW_LIKES_EXP'             => 'Exibe a contagem de gostos PostLove junto aos tópicos recentes.',

	//configuração geral para usuários anônimos
	'RT_OVERRIDABLE'                => 'UCP configurações substituíveis',
	'RT_LOCATION'                   => 'Exibir localização',
	'RT_LOCATION_EXP'               => 'Selecione o local para exibir tópicos recentes.',
	'RT_TOP'                        => 'Mostrar no topo',
	'RT_BOTTOM'                     => 'Mostrar no fundo',
	'RT_SIDE'                       => 'Mostrar no lado',
	'RT_SORT_START_TIME'            => 'Ordenar tópicos pela hora de início',
	'RT_SORT_START_TIME_EXP'        => 'Habilite para classificar tópicos recentes pela hora de início do tópico, em vez da última hora de publicação.',
	'RT_UNREAD_ONLY'                => 'Mostrar apenas tópicos não lidos',
	'RT_UNREAD_ONLY_EXP'            => 'Ativar para exibir somente tópicos não lidos (se eles são "recentes" ou não). Esta função usa as mesmas configurações (excluindo fóruns / tópicos etc.) como modo normal. Nota: isso só funciona para usuários conectados; Os convidados receberão a lista normal.',
	'RT_RESET_DEFAULT'              => 'Redefinir as configurações do usuário',
	'RT_RESET_DEFAULT_EXP'          => 'Redefinir as configurações do usuário para o padrão.',

	//Version checker
	'RT_VERSION_CHECK'				=> 'Verificação de Versão',
	'RT_LATEST_VERSION'				=> 'Última versão',
	'RT_EXT_VERSION'				=> 'Versão de extensão',
	'RT_CHECK_UPDATE'				=> 'Verifique <a href="https://www.avathar.be/forum/app.php/dlext/details?df_id=35">avathar.be</a> para ver se há atualizações disponíveis.',

	//Standalone pages
	'RT_PAGES'                      => 'Páginas independentes',
	'RT_PAGE_ENABLE'                => 'Ativar páginas autónomas',
	'RT_PAGE_ENABLE_EXP'            => 'Permitir o acesso às páginas autónomas de tópicos recentes. Isto é independente da exibição na página de índice.',
	'RT_PAGE'                       => 'Página completa',
	'RT_PAGE_EXP'                   => 'Página independente de Tópicos Recentes com cabeçalho e rodapé completos do fórum.',
	'RT_SIMPLE_PAGE'                => 'Página simplificada',
	'RT_SIMPLE_PAGE_EXP'            => 'Página simplificada de Tópicos Recentes sem cabeçalho e rodapé do fórum, adequada para incorporação em um iframe.',
	'RT_VIEW_PAGE'                  => 'Ver página em nova aba',

	//Bloco publicitário
	'RT_ADS_SETTINGS'           => 'Bloco publicitário',
	'RT_ADS_ENABLE'             => 'Activar bloco publicitário',
	'RT_ADS_ENABLE_EXP'         => 'Exibe um bloco HTML personalizado junto aos tópicos recentes na página inicial. Apenas visível quando a localização está definida como "Lateral".',
	'RT_ADS_CODE'               => 'HTML publicitário',
	'RT_ADS_CODE_EXP'           => 'Introduza HTML personalizado para exibir no bloco publicitário (por exemplo, código de anúncios, botão de doação ou outro conteúdo).',

	//Donation
	'PATREON_ALT'                => 'Torne-se um patrono',
	'RT_DONATE'					=> 'Doação para RecentTopics',
	'RT_DONATE_SHORT'			=> 'Faça uma doação para RecentTopics',
	'RT_DONATE_EXPLAIN'			=> 'RecentTopics é 100% gratuito. É um projeto de hobby no qual estou gastando meu tempo e dinheiro, apenas por diversão. Se você gosta de usar RecentTopics, considere fazer uma doação. Eu realmente apreciaria isto. Sem condições.',
	)
);
