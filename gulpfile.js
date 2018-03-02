var gulp = require('gulp');
var uncss = require('gulp-uncss');
gulp.task('default', function(){
    console.log( "ne fait rien!!");
}) 
gulp.task('cleancss', function () {
    return gulp.src('./css/application.css')
        .pipe(uncss({
            html: ['https://api.poleterresolide.fr']
        }))
        .pipe(gulp.dest('./out'));
});