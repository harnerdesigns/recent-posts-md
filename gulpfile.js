// Gulp Dependencies

var gulp = require('gulp'),
    gutil = require('gulp-util'),
    sass = require('gulp-sass'),
    uglify = require('gulp-uglify'),
    concat = require('gulp-concat'),
    rename = require('gulp-rename'),
    header = require('gulp-header'),
    sourcemaps = require('gulp-sourcemaps'),
    package = require('./package.json'),
    zip = require('gulp-zip'),
    autoprefixer = require('gulp-autoprefixer'),
    browserSync = require('browser-sync').create(),
    bump = require('gulp-bump');




// Env Vars

var browserSyncProxy = "localhost/blank2";


var sassFiles = ["scss/*.scss"];
var sassDest = "./";

var jsFiles = [];
var jsDest = "./"

var watchSassFiles = ['scss/**/*.scss'];
var watchJsFiles = [''];
var watchPhpFiles = ['**/*.php'];

var buildFiles = ["*.css", "*.php", "*.js", "*.md", "!build/", "!.map", "!scss/", "!gulpfile.js"]

var banner = {
    full: '/*!\n' +
        ' * <%= package.name %> v<%= package.version %>: <%= package.description %>\n' +
        ' * (c) ' + new Date().getFullYear() + ' <%= package.author.name %>\n' +
        ' * MIT License\n' +
        ' * <%= package.repository.url %>\n' +
        ' * Open Source Credits: <%= package.openSource.credits %>\n' +
        ' */\n\n',
    min: '/*!' +
        ' <%= package.name %> v<%= package.version %>' +
        ' | (c) ' + new Date().getFullYear() + ' <%= package.author.name %>' +
        ' | <%= package.license %> License' +
        ' | <%= package.repository.url %>' +
        ' | Open Source Credits: <%= package.openSource.credits %>' +
        ' */\n',
    theme: '/*!\n' +
        ' * Theme Name: <%= package.name %>\n' +
        ' * Theme URI: <%= package.repository.url %>\n' +
        ' * GitHub Theme URI: <%= package.repository.url %>\n' +
        ' * Description: <%= package.description %>\n' +
        ' * Version: <%= package.version %>\n' +
        ' * Author: <%= package.author.name %>\n' +
        ' * Author URI: <%= package.author.url %>\n' +
        ' * License: <%= package.license %>\n' +
        ' * ' +
        ' */'
};





gulp.task('sass', gulp.series(function(done) {
    gulp.src(sassFiles)
        .pipe(sourcemaps.init())
        .pipe(sass({ sourceComments: 'map' }))
        .on('error', gutil.log)
        .pipe(autoprefixer({
            browsers: ['last 2 versions'],
            cascade: false
        }))
        .pipe(sourcemaps.write(sassDest))
        .pipe(gulp.dest(sassDest))
        .pipe(browserSync.stream());
    done();
}));




gulp.task('js', gulp.series(function() {
    return gulp.src(jsFiles)
        .pipe(sourcemaps.init())
        .pipe(concat('theme.js'))
        .pipe(uglify())
        .pipe(sourcemaps.write(jsDest))

        .pipe(gulp.dest(jsDest));
}));


gulp.task('bump:php', function(){
 return gulp.src("./recent-posts-md.php")
  .pipe(bump())
  .pipe(gulp.dest('./'));
});

gulp.task('bump:package', function(){
 return gulp.src("./package.json")
  .pipe(bump())
  .pipe(gulp.dest('./'));
});

gulp.task("bump", gulp.series("bump:php", "bump:package"))





gulp.task('watch', gulp.series(function() {
    gulp.watch(watchSassFiles, gulp.series('sass'));
    gulp.watch(watchJsFiles, gulp.series('js'));


}));

gulp.task("build:zip", function(){
var updatedPackage = require("./package.json");
return gulp.src(buildFiles)
        .pipe(zip(updatedPackage.name + "-" + updatedPackage.version+".zip"))
        .pipe(gulp.dest('./build'));
    
})


gulp.task('build', gulp.series('bump', 'build:zip'));







gulp.task('serve', gulp.parallel(function() {

    browserSync.init({
        proxy: browserSyncProxy
    });
}, function() {

    gulp.watch(watchSassFiles, gulp.series('sass'));

    gulp.watch(watchJsFiles, gulp.series('js'));

    gulp.watch(watchPhpFiles).on('change', browserSync.reload);

    gulp.watch("theme.js").on('change', browserSync.reload);
}));

gulp.task('default', gulp.series('sass', 'serve'));