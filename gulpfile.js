'use strict';

var gulp = require('gulp'),
    minifycss = require('gulp-minify-css'),
    uglify = require('gulp-uglify'),
    concat = require('gulp-concat'),
    rename = require('gulp-rename'),
    runSequence = require('run-sequence'),
    bower = require('gulp-bower');

gulp.task('bower', function () {
    return bower();
});

gulp.task('styles', function () {
    return gulp.src([
        'src/resources/bower_components/bootstrap/dist/css/bootstrap.min.css',
        'src/resources/css/flat-ui.min.css',
        'src/resources/css/demo.css'
    ])
        .pipe(concat('all.min.css'))
        .pipe(minifycss())
        .pipe(gulp.dest('web/dist/css'));
});

gulp.task('fonts', function () {
    gulp.src('src/resources/fonts/**/*.{ttf,woff,eof,svg}')
        .pipe(gulp.dest('web/dist/fonts'));
});

gulp.task('images', function () {
    gulp.src('src/resources/images/**/*')
        .pipe(gulp.dest('web/dist/images'));
});

gulp.task('scripts', function () {
    return gulp.src(['src/resources/js/**/*.js'])
        .pipe(concat('main.js'))
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest('web/dist/js'));
});

gulp.task('zepto', function () {
    gulp.src('src/resources/bower_components/zepto/zepto.min.js')
        .pipe(gulp.dest('web/dist/js'));
});

gulp.task('build', function (callback) {
    runSequence('bower', 'styles', 'fonts', 'images', 'scripts', 'zepto', callback);
});

gulp.task('default', ['build']);

gulp.task('watch', function() {
    // Watch CSS files
    gulp.watch('src/resources/css/**/*.css', ['styles']);

    // Watch JS files
    gulp.watch('src/resources/js/**/*.js', ['scripts']);

    // Watch image files
    gulp.watch('src/resources/images/**/*', ['images']);
});
