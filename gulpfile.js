'use strict'

const gulp = require("gulp")
const { parallel } = require("gulp")
const sass = require("gulp-sass")
const cleanCss = require("gulp-clean-css")
const concat = require('gulp-concat')
const uglify = require('gulp-uglify-es').default
const sourcemaps = require('gulp-sourcemaps')

function css() {
    return gulp.src('./lib/tsm.scss')
        .pipe(sourcemaps.init())
        .pipe(sass())
        .pipe(cleanCss())
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('./lib'))
}

function watchCss() {
    gulp.watch('./lib/tsm.scss', parallel('css'))
}

function js() {
    return gulp.src('./lib/tsm.js')
        .pipe(sourcemaps.init())
        .pipe(uglify())
        .pipe(concat('tsm.min.js'))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('./lib'))
}

function watchJs() {
    gulp.watch('./lib/tsm.js', parallel('js'))
}

exports.css = css
exports.js = js
exports.watch = gulp.parallel(watchCss, watchJs)