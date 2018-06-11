// gulpfile.js - Gerencia, automatiza e inicia um servidor dos arquivos 

// Site       : --
// Autor      : Marlon de Oliveira dos Santos <marlonlolz75@gmail.com>
// Manutenção : --



// --------------------------------------------------------------------------

//  Este programa automatiza os arquivos, minificando, concatenando e convertendo
//  eles para tornar leves. Também inicia um servidor que atualiza a aba do browser 
//  automaticamente assim que uma mudança nos arquivos na lista de observação é 
//  detectada.


//  Principais Exemplos:
//   1. ./app/client$ gulp
//    Roda a tarefa default, gera a pasta dist com todas os arquivos declarados
//    na variável path concatenados, minificados e convertidos.

//   2. ./app/client$ gulp serve
//    Assiste todos os arquivos das variáveis path.CSS, path.VENDOR, path.TEMPLATING,
//    path.JS, path.FILES, path.INDEX, é iniciado um servidor com a pasta dist/ como 
//    pasta principal, e aberto uma aba no browser principal do usuário, e caso alguma
//    mudança ocorra nos arquivos assistidos, é inicializada a tarefa correspondente a 
//    esse arquivo e então atualizado a aba aberta no browser.

//   3. ./app/client$ gulp clean
//    Deleta todos os arquivos da pasta dist/

//  Caso de uso recomendado:
//     1. ./app/client$ gulp default serve
//       Recomendo utilizar este script desta maneira pois ele gera todos os arquivos da 
//       dist e então inicia um servidor, caso algum novo arquivo seja adicionado,esse
//       é o procedimento recomendado.
// -------------------------------------------------------------------------------



// Histórico:

//   v1.0 2018-03-10, Marlon:
//     - Versão inicial.



// Licença: GPL.



// Depencias do script
var gulp                  = require('gulp');
var pug                   = require('gulp-pug');    //Pacote que converte arquivos em .pug (html 'minificado') para html
var clean                 = require('gulp-clean');  //Pacote que deleta arquivios
var watch                 = require('gulp-watch');  //Pacote que assiste mudanças em arquivos
var uglify                = require('gulp-uglify'); //Pacote que converte arquivos em javascript para versões com menos bytes (tira espaços, comentários, reduz nomes de variáveis)
var concat                = require('gulp-concat'); //Pacote que concatena arquivos
var imagemin              = require('gulp-imagemin'); //Pacote para comprimir imagens
var addStream             = require('add-stream');  // Pacote que permite adicionar uma tarefa do gulp dentro de outra tarefa
var minifyCSS             = require('gulp-clean-css');  //Pacote que converte arquivos em cSS para versões com menos bytes
var concat_vendor         = require('gulp-concat-vendor'); //Pacote para concatenar arquivos de bibliotecas de terceiros
var angularTemplateCache  = require('gulp-angular-templatecache'); //Pacote para encapsular arquivos html em variáveis de template cache do Angular
var browserSync           = require('browser-sync').create(); //Pacote para iniciar um servidor e abrir uma aba no browser princpal que aponta para esse servidor

// Atalho para a função do browserSync que atualiza a aba no browser
var reload                = browserSync.reload;


var path = {
  // '**' Significa: Todos os níveis de pastas 
  // '*' Significa: Qualquer nome de arquivos

  // Arquivos que devem ser convertidos de pug para html e encapsulados nas variavéis 
  // de template cache do Angular
  TEMPLATING: [
  	'src/views/**/*.pug',
    'src/js/**/*.pug',
  ],
  // Arquivos que pertencem ao indexo e devem ser movidos para a /dist e convertidos
  // de pug para html
  INDEX: [
    'src/index.pug',
    'src/webview.pug'
  ],
  // Arquivos que devem ser movidos para a pasta /dist
  FILES: [
    'src/.htaccess', 
    'src/favicon.png',
    'src/libs/**/*'
  ],
  // Arquivos javascript que devem ser minificados, concatenados e movidos para
  // /dist/js/app.js
  JS: [ 
    'src/js/**/*.js',
    'src/views/**/*.js'
  ],
  // Arquivos css que devem ser minificados, concatenados e movidos para a 
  // /dist/css/styles.css
  CSS: [
    'src/css/*.css',
    'node_modules/v-accordion/dist/v-accordion.min.css',
    'node_modules/ng-dialog/css/ngDialog.min.css',
    'node_modules/angular-tooltips/dist/angular-tooltips.min.css',
    'node_modules/ng-dialog/css/ngDialog-theme-default.min.css',
    'node_modules/ng-table/bundles/ng-table.min.css',
    'node_modules/sweetalert2/dist/sweetalert2.min.css',
    'node_modules/angularjs-toaster/toaster.min.css'
  ],
  // Imagens que devem ser comprimidas e movidas para /dist/img
  IMG: [
  	'src/img/**'
  ],
  // Pacotes de terceiros que devem ser comprimidos e movidos para dist/js/vendor.js
  VENDOR: [
    'node_modules/angular/angular.js',
    'node_modules/@uirouter/angularjs/release/angular-ui-router.js',
    'node_modules/angular-animate/angular-animate.js',
    'node_modules/ng-dialog/js/ngDialog.js',
    'node_modules/sweetalert2/dist/sweetalert2.js',
    'node_modules/angularjs-toaster/toaster.js',
    'node_modules/angular-jwt/dist/angular-jwt.js',
    'node_modules/v-accordion/dist/v-accordion.js',
    'node_modules/angular-paging/src/paging.js'
  ],
  // Pasta de destino dos arquivos que passam por tarefas de minificação,
  // compressão, concatenação, conversão, ou simplesmente troca de diretório
  DIST: './dist'
};

// Inicia servidor browserSync
gulp.task('browserSync', function() {
  browserSync.init({
    server: {
      baseDir: path.DIST
    },
  })
})

// Apaga todos os arquivos e todos os diretórios da pasta dist/
gulp.task('clean', function() {
	return gulp.src(['./dist/**/*'], {read: false})
		.pipe(clean()); // Deleta arquivos
});

// Minifica, concatena e move concatena e move para a pasta dist/ todos os
// arquivos da variavel path.CSS e então atualiza a aba do browser (caso existente)
gulp.task('css', function () {
  gulp.src(path.CSS)  // Seleciona arquivos
    .pipe(minifyCSS()) // Minifica o CSS
    .pipe(concat('styles.css')) // Concatena todos os arquivos selecionados para o arquivos styles.css
    .pipe(gulp.dest(path.DIST + '/css/')) // Move o arquivo criado styles.css para a pasta dist/csss/
    .pipe(reload({stream:true})); // Atualiza a aba do browser (caso existente)
});

// Converte arquivos da variavel path.TEMPLATING de pug para HTML, encapsula
// eles em variaveis de template cache do angular
function prepareTemplates() {
  return gulp.src(path.TEMPLATING)  // Seleciona arquivos
    .pipe(pug()) // Converte os arquivos selecionados de pug para html
    .pipe(angularTemplateCache()); // Encapsula arquivos convertidos para variaveis template cache do angular
}

// Concatena arquivos da variavel path.JS, executa a função prepareTemplates() e 
// concatena as variaveis template cache do angular geradas pela função 
// prepareTemplates() junto com os arquivos já concatenados do diretório path.JS
// em seguida minifica todo o código final javascript e move para a pasta dist/js/app.js
// e atualiza a aba do browser (caso existente)
gulp.task('js', function () {
  gulp.src(path.JS) // Seleciona arquivos
    .pipe(concat('app.js')) // Concatena arquivos para o arquivos app.js
    .pipe(addStream.obj(prepareTemplates())) // Executa função prepareTemplates()
    .pipe(concat('app.js')) // Concatena arquivos já concatenados com os arquivos resultantes da função prepareTemplates() para o arquivos app.js
    .pipe(uglify({mangle: false})) // Minifica todo código javascript final
    .pipe(gulp.dest(path.DIST + '/js')) // Move arquivos para dist/js/app.js
    .pipe(reload({stream:true})); // Atualiza a aba do browser (caso existente)
});

// Concatena todas as bibliotecas de terceiros da variavel path.VENDOR, minifica 
// e move para dist/js/vendor.js, atualiza a aba do browser (caso existente)
gulp.task('vendor', function () {
	gulp.src(path.VENDOR) // Seleciona arquivos
    .pipe(concat('vendor.js')) // Concatena arquivos selecionados para o arquivos vendor.js
		.pipe(uglify({mangle: false})) // Minifica código javascript concatenado
    .pipe(gulp.dest(path.DIST + '/js')) // Move para a pasta dist/js/vendor.js
    .pipe(reload({stream:true})); // Atualiza a aba do browser (caso existente)
});

gulp.task('files', function(){
  // Seleciona arquivos da variavel path.FILES, move eles para a pasta dist/ e
  // atualiza a aba do browser (caso existente)
  gulp.src(path.FILES, {base: 'src'}) // Seleciona arquivos
    .pipe(gulp.dest(path.DIST)) // Move para dist/
    .pipe(reload({stream:true})); // Atualiza a aba (caso existente)
  // Seleciona arquivos da variavel path.INDEX, converte código pug para html move 
  // código convertido para a pasta dist/ e atualiza a aba do browser ()
  gulp.src(path.INDEX, {base: 'src'})
    .pipe(pug()) // Converte pug para html
    .pipe(gulp.dest(path.DIST)) // Move para dist/
    .pipe(reload({stream:true})); // Atualiza aba (caso existente)
    
});

// Seleciona arquivos da pasta path.IMG, comprime as imagens selecionadas e move
// para a pasta dist/img/ e atualiza aba do browser (caso existente) 
gulp.task('img', function(){
  gulp.src(path.IMG) // Seleciona arquivos
    .pipe(imagemin()) // Comprime imagens
    .pipe(gulp.dest(path.DIST + '/img')) // Move para pastar dist/img/
    .pipe(reload({stream:true})); // Atualiza aba do broser (caso existente)
});

// Assiste arquivos, caso detectar alguma mudança, realiza a função dentro dos colchetes
gulp.task('watch', function () {
  gulp.watch(path.CSS, ['css']);
  gulp.watch(path.VENDOR, ['vendor']);
  gulp.watch(path.TEMPLATING.concat(path.JS), ['js']);
  gulp.watch(path.FILES.concat(path.INDEX), ['files']);
});

// Definição de 'bundle' de tarefas
gulp.task('default', ['css', 'js', 'vendor', 'files', 'img']);
gulp.task('serve', ['watch', 'browserSync']);