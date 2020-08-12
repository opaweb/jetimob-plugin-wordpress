=== Jetimob ===
Contributors: opaweb
Tags: jetimob, crm, imobiliária, imóveis
Requires at least: 4.8
Tested up to: 5.3
Stable tag: 3.1
License: GPLv2 ou mais recente
License URI: https://www.gnu.org/licenses/gpl-2.0.html

=== Description ===

Plugin de integração do CRM imobiliário [Jetimob] (https://jetimob.com) com o WordPress. Com este plugin, você poderá manter sincronizados os imóveis do CRM e do seu site.

=== Installation ===
1. Baixe o plugin e extraia seu conteúdo na pasta wp-content/plugins/ ou instale diretamente pelo painel de Plugins de sua instalação WordPress.
2. Ative o plugin na tela "Plugins" do seu site.
3. Acesse o menu Jetimob para ajustar as opções do plugin.
4. Na tela de opções, não esqueça de inserir as chaves de API para sincronização dos imóveis e envio dos leads para o Jetimob. Estas opções encontram-se na aba "Jetimob - Geral".
5. Para a sincronização dos imóveis ser realizada, é necessária a configuração de uma tarefa cron em seu servidor. Consulte a documentação de sua hospedagem ou painel de controle para saber como proceder para realizar a configuração. A tarefa cron deve configurada para executar o comando "php -q http://URL/wp-content/plugins/jetimob/start.php", substituindo o 'URL' pela url do seu site. Se o seu site usa HTTPS - o que recomendamos - substitua "http" por "https". Recomendamos que a sincronização seja feita no máximo duas vezes por dia, uma taxa de requisições diárias elevado ocasionará um bloqueio no IP do seu site, impedindo que os imóveis sejam sincronizados. Para configurar a tarefa manualmente para execução a cada hora, utilize como configuração do tempo de execução "0 */1 * * *", podendo substituir os horários para outros de sua preferência. 

Caso o script de importação trave ao executar, é necessário executar cada passo da importação manualmente.
Substitua a entrada cron que contém o arquivo start.php e insira estas, respeitando esta ordem:
"php -q http://URL/wp-content/plugins/jetimob/refresh.php"
"php -q http://URL/wp-content/plugins/jetimob/imoveis_add.php"
"php -q http://URL/wp-content/plugins/jetimob/imoveis_delete.php"
"php -q http://URL/wp-content/plugins/jetimob/imoveis_update.php"

Novamente, substitua URL pelo endereço do seu site e http por https, quando aplicável.


== Changelog ==

= 3.1.3 = 
* Atualização dos endpoints da api - Novo Jetimob

= 3.1.2 =
* Correção de retrocompatibilidade com versão anterior do plugin.

= 3.1.1 =
* Correção de função inválida.

= 3.1 =
* Definição de requisitos mínimos de PHP para utilização do plugin. Se não atendidos, a instalação falha. (PHP 7.2.24 e WP 4.8).
* Inclusão de método de atualização direta via GitHub

= 3.0 =
* Nova versão. Mecanismo de importação reescrito. O plugin cria os post types necessários para seu funcionamento, independente do tema utilizado.

= 2.1 =
* Implementação de teste de compatibilidade para servidores com função exec habilitada ou desabilitada. Recomenda-se usar a função exec() para uma maior confiabilidade na conexão com o servidor do Jetimob.

= 2.0 =
* Alteração de todo o mecanismo de importação.
* Não é mais necessário deletar todos os imóveis para atualização de dados.
* Formatação de URL's padronizada 'código-título'.
* Compatibilidade específica com temas Houzez e Realia(plugin e temas derivados).
* Implementada API de integração de Leads -> para o plugin Gravity Forms.


= 1.1 =
* Primeira versão pública
* Implementada API de integração de Leads

= 1.0 =
* Versão final de testes.

= 0.5 =
* Primeiro release de testes liberado.

== Upgrade Notice ==

= 2.0 =
Alteração do mecanismo de importação - os imóveis não são mais deletados.

= 1.1 =
Implementação de novo recurso.

= 1.0 =
Correção de diversos bugs. Atualização recomendada.

= 0.5 =
Primeira implementação dos recursos de integração.

~Current Version:3.1.1~
