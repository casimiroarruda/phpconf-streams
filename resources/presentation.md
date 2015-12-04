%title: PHP Conference Brail 2015 - Streams
%author: Anderson 'Duodraco' Casimiro
%date: 2014-09-22

-> Streams
===

-> vamos falar sobre esse ilustre desconhecido
---





-> Já visitou http://php.net/streams? Se ainda não fez isso, você deveria.
-> Stream é qualquer fluxo de dados para o PHP e nossa plataforma possui uma 
-> extensa e excelente API a explorar.

^
-> O que são Streams? Onde vivem? O que trafegam? Como se reproduzem?
-> Esta tarde, no PHPConf Reporter.

---

-> Boa Tarde
===

-> No programa desta tarde
---

* De onde conheço isso?

^
* O que são?

^
* O que preciso saber para usar?

^
* O que o *PHP* já tem pronto?

^
* Dizer para a _Stream_ "Agora vou lhe usar"

^
* Ideias para seu projeto
^
    - Você pode substituir _http://_ por _inhame://_ por exemplo

^
* Sério que dá pra usar na prática?
^
    - Sério

---

-> O que são Streams
===

-> Streams não é algo tão distante
---

-> Já usou *file_get_contents*?

^
_$file_ = *file_get_contents*('C:\\Arquivos de Programas\\Projeto\\lista.csv');

^
_$file_ = *file_get_contents*( *implode*(DIRECTORY_SEPARATOR.[__DIR__,'Projeto','lista.csv']));

^
-> E com _http_?

^
$license = *file_get_contents*('http://www.php.net/license/3_01.txt');
^

-> E que tal pegar a licença do Composer?
^

$license = *file_get_contents*('phar://composer.phar/LICENSE');

---

-> O que são Streams?
===

-> Introdução
---

-> Tudo começou (há um tempo atrás, na ilha do Sol ... ) nos idos do *PHP 4.3*.

^
-> Era uma iniciativa de uniformizar operações diversas que usavam o mesmo 
-> conjunto de funções.

^
-> Resumidamente podemos dizer que um *Stream* é um fluxo de dados que pode 
-> sofrer leitura e escrita de maneira linear.
-> Também devemos ser capazes de ir direto a um ponto nessa cadeia.

^
-> Por _fluxo de dados_ entenda-se: Arquivos, Containers de Arquivos, Mensagens 
-> em rede, etc...

---

-> O que são Streams?
===

-> Introdução
---

-> Que tal de maneira prática?


---

-> Conceitos
===

-> O que precisamos saber?
---

-> Basicamente precisamos conhecer os seguintes itens:

^
-> *Socket*

^
-> *Filter*

^
-> *Context*

^
-> *Wrapper*


---

-> Sockets
===

-> Conceitos
---

-> Sockets são, por definição, canais de comunicação de dados. Você pode criar uma
-> mensagem Socket já no [IP](https://pt.wikipedia.org/wiki/Protocolo_de_Internet). Alguém aqui lembra do [Modelo OSI](https://pt.wikipedia.org/wiki/Modelo_OSI)?
-> O IP fica na Camada de Rede.

-> Podemos facilitar um pouco as coisas usando protocolos de comunicação, como *UDP* e *TCP*.

-> Precisamos de um Servidor localizável e um cliente para essa comunicação.

^
-> Obviamente isso não se restringe somente a conexões de rede, não é mesmo [PHP FPM](http://php.net/manual/install.fpm.php)?

---

-> Sockets
===

-> Criando um servidor:
---

3.  $socket = *stream_socket_server*("tcp://127.0.0.1:32223", $errno, $errstr);
4.  *if* (!$socket) {
5.      *echo* "{$errstr} ({$errno})" . PHP_EOL;
6.  } *else* {
7.      *while* ($conn = *stream_socket_accept*($socket)) {
8.          *echo* *fread*($conn,1024);
9.          $horas = *date*('G').' horas com '.((int)*date*('i')).' minutos';
10.         *fwrite*($conn, "Agora são {$horas} Silviooo.". PHP_EOL);
11.         *fclose*($conn);
12.     }
13.     *fclose*($socket);
14. }

---

-> Sockets
===

-> Criando um cliente:
---

3.  $client = *stream_socket_client*("tcp://127.0.0.1:32223", $errno, $errstr, 30);
4.  *if* (!$client) {
5.      echo "$errstr ($errno)".PHP_EOL;
6.  } *else* {
7.      *fwrite*($client, "Mah que horas são Lombardi?".PHP_EOL);
8.      *while* (!*feof*($client)) {
9.          echo *fgets*($client, 1024);
10.     }
11.     *fclose*($client);
12. }

^
Criamos o *ALP*: Abravanel Lombardization Protocol

---

-> Filters
===

-> Conceitos
---

-> Como o nome já diz, *Filters* servem basicamente como um _proxy_ para o fluxo
-> de dados.

^
-> Já há alguns Filters built in no PHP. Use *stream_get_filters()* para obter uma lista.

^
-> Você também pode criar seus filtros.

---

-> Filters
===

-> Aplicando
---

-> Vamos modificar um pouco nosso client

3. $client = *stream_socket_client*("tcp://127.0.0.1:32223", $errno, $errstr, 30);
4. *stream_filter_append*($client, 'string.toupper');
5. /\* ... \*/

---

-> Filters
===

-> Criando um Filter
---

2. class *php_user_filter*
3. {
4.     public $filtername;
5.     public $params;
6.     public *filter* ( _resource_ $in , _resource_ $out , _int_ &$consumed , _bool_ $closing ) : _int_
7.     public *onClose* () : _null_
8.     public *onCreate* () : _bool_
9. }

---

-> Filters
===

-> Usando um Filter customizado
---

2.  *class* Jequitinator *extends* php_user_filter
3.  {
4.      *public function* filter($in, $out, &$consumed, $closing)
5.      {
6.          *while* ($bucket = *stream_bucket_make_writeable*($in)) {
7.              $s = $bucket->data;
8.              *if* (*rand*(5, 7) === 7) {
9.                  $bucket->data = *wordwrap*($s,strlen($s) \* .7," [Jequiti] ");
10.             }
11.             $consumed += $bucket->datalen;
12.             *stream_bucket_append*($out, $bucket);
13.         }
14.         *return* _PSFS_PASS_ON_;
15.     }
16. }
17. *stream_filter_register*('jequiti', 'Jequitinator');
18. $client = *stream_socket_client*("tcp://127.0.0.1:32223", $errno, $errstr, 30);
19. *stream_filter_append*($client, 'jequiti');

---

-> Context
===

-> Conceitos
---

-> Context é um conjunto de parâmetros e opções específicas de *Wrappers*, 
-> que modificam o comportamento das Streams. 

^
-> Parâmetros são configurações de uso geral. Há na verdade somente um parâmetro
-> possível: [notification](http://php.net/manual/function.stream-notification-callback.php)

^
-> Opções são específicas para cada protocolo/Wrapper. Você pode configurá-las 
-> ao usar *stream_context_create* ou *stream_context_set_option*

^
-> Para saber mais sobre Opções específicas acesse a [seção no manual](http://php.net/manual/en/context.php)

---

-> Context
===

-> Configurando um Contexto para um Stream http
---

2.  *$opts* = [
3.      'http' => [
4.          'method' => 'POST',
5.          'header' => "Content-type: application/x-www-form-urlencoded\r\n".
6.          'content' => ['user'=>'galvao@php.conf.br','pw'=>'meTraz1P0l4r!'];
7.      ]
8.  ];
9.  *$context* = stream_context_create(*$opts*);
10. *$result* = file_get_contents('http://phpconf.com.br/auth', false, *$context*);

---

-> Wrappers
===

-> Conceitos
---

-> Wrapper é um mecanismo do PHP que encapsula o funcionamento de uma determinado
-> protocolo. Quando estamos acessando um arquivo via *file_get_contents* por exemplo
-> estamos usando o Wrapper *file://*. Ou quando usamos o *http://* onde a complexidade
-> do protocolo fica abstraída de maneira a acessarmos um arquivo/recurso em um servidor web.

^
-> Há vários Wrappers incorporados no PHP permitindo-nos acessá-los quase que sem
-> maiores configurações. Inclusive ao usar o Google App Engine temos acesso a um 
-> protocolo customizado para o Cloud Storage, o [gs://](https://cloud.google.com/appengine/docs/php/googlestorage/)

---

-> Wrappers
===

-> Wrappers embutidos
---

*file://*   Accessing local filesystem

*http://*   Accessing HTTP(s) URLs

*ftp://*    Accessing FTP(s) URLs

*php://*    Accessing various I/O streams

*zlib://*   Compression Streams

*data://*   Data (RFC 2397)

*glob://*   Find pathnames matching pattern

*phar://*   PHP Archive

*ssh2://*   Secure Shell 2

*rar://*    RAR

*ogg://*    Audio streams

*expect://* Process Interaction Streams

---

-> Wrappers
===

-> Escrevendo Wrappers
---

-> Estude o [modelo de classe](http://php.net/manual/en/class.streamwrapper.php) no manual 

*dir_closedir*() : bool;
*dir_opendir*(string $path, int $options):bool;
*dir_readdir*()*:string;
*dir_rewinddir*():bool;
*mkdir*(string $path, int $mode, int $options):bool;
*rename*(string $path_from, string $path_to):bool;
*rmdir*(string $path, int $options):bool;
*stream_cast*(int $cast_as); //resource
*stream_close*();
*stream_eof*():bool;
*stream_flush*():bool;
*stream_lock*(int $operation):bool;
*stream_metadata*(string $path, int $option, $value):bool;
*stream_open*(string $path,string $mode,int $options, string &$opened_path):bool;
*stream_read*(int $count):string;
*stream_seek*(int $offset, int $whence = SEEK_SET):bool;
*stream_set_option*(int $option, int $arg1, int $arg2):bool;
*stream_stat*():array;
*stream_tell*():int;
*stream_truncate*(int $new_size):bool;
*stream_write*(string $data):int;
*unlink*(string $path):bool;
*url_stat*(string $path, int $flags):array;

---

-> Wrapper
===

-> Que tal trocar _http_ por _inhame_?
---

2.  class *InhameWrapper*
3.  {
4.      protected $resource;
5.      const BASEDIR = '/tmp';
6.  
7.      public function *stream_open*($path, $mode, $options, &$opened_path)
8.      {
9.          $path = str_replace('inhame://', '', $path);
10.         $filePath = self::BASEDIR . DIRECTORY_SEPARATOR . $path;
11.         if (file_exists($filePath)) {
12.             $this->resource = fopen($filePath, 'r');
13.         }
14.         *return* true;
15.     }
16.     /\* ... \*/

2.  require *'InhameWrapper.php'*;
3.  stream_wrapper_register( *'inhame'*, *'InhameWrapper'*);
4.  echo file_get_contents( *'inhame://oi_eu_sou_o.Goku'*);

---

-> Streams
===

-> Vamos criar algo mais util?
---

-> *Desafio*

-> Criar um Wrapper para usar file\_\*\_contents com SQLite.

---

-> Streams
===

-> Conclusão
---



-> Com o uso de *Streams* e/ou de sua API você ganha o poder de manipular os dados

-> de sua aplicação de maneira geral. Você pode abstrair protocolos de comunicação,

-> criar filtros para os dados, customizar o uso do mesmo protocolo e muito mais...

-> O melhor, usando funções simples do PHP.


-> Que usos mais você pode dar? Estou fazendo uma camada de comunicação para IoT ;)

---





> You must be shapeless, formless, like water. When you pour water in a cup, 
> it becomes the cup. When you pour water in a bottle, it becomes the bottle. 
> When you pour water in a teapot, it becomes the teapot. Water can drip and it 
> can crash. Become like water my friend.

-> Bruce Lee

---

-> Anderson Casimiro

-> *duodraco*

-> Cofundador + CTO @ *Agrosmart*
-> Cofundador + Colaborador @ *PHPSP*

-> (https://github.com/duodraco/phpconf-streams)