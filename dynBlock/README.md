# scriptcase-libs
# dynBlock

Como desenvolvedores scriptcase devem saber as libs do scriptcase adicionam funções ao código ou criam classes para uso nas aplicações.
Essa lib daqui tem como objetivo disponibilizar de maneira fácil de usar um bloco dinâmico, no qual o bloco pode ser replicado.

Existe uma alteração feita no jQuery principal do scriptcase (\prod\third\jquery\js\jquery.js), 
foi adicionado o código da biblioteca: http://robinherbots.github.io/Inputmask

Com isso todo aplicação vai ter disponibilizada para ela as funções dessa biblioteca, a mesma é utilizada dentro da biblioteca dynBlock.

As funções presente na lib são:
* Identificação do bloco as ser replicado por um campo
* Método para atualizar dados em evento ajax
* Header geral dos blocos
* Replicação do header do bloco caso o mesmo esteja presente
* Controle automático de botões adição e exclusão
* Integração com o inserir e atualizar padrão
* Integração com refresh do scriptcase
* Disparo de eventos ajax originais presentes no campos do bloco
* Máscaras nos campos utilizando a biblioteca InputMask

ps.: Ainda vou adicionar comentários ao código para explicar o porquê de cada parte do mesmo.
