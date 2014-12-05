module.exports = function(grunt) {
  'use strict';


  var _ = require('lodash'),
      fs = require('fs'),
      path = require('path'),
      crypto = require('crypto'),
      taskName = 'copyChanged',
      options, done, timerid, restartTimer, tryCount, firstExecute =  true,
      watchers = [], tasks = [], dict = {};

  grunt.registerTask(taskName, 'task description', function(){

    var executedThisTime = false;

    options = this.options({
      src: ['.'],
      dest: '__modified',
      checksum: false,
      watchTask: false
    });


    grunt.log.ok('Waiting...');
    done = this.async();

    function onDirChange(event, filename, dir) {
      var filepath = path.join(dir || '', filename || '').replace(/\\/g, '/');

      if( !fs.existsSync(filepath) ) return;

      if (fs.statSync(filepath).isDirectory()) {
        startWatch = _.debounce(startWatch, 1000);
      } else {
        onFileChange(filepath);
      }
    }

    function onFileChange(filepath){
      if(! executedThisTime ) {
        grunt.log.writeln('');
        grunt.log.ok('"copyChanged" File changed.'.yellow);
        executedThisTime = true;
      }
      grunt.verbose.writeln('File Changed: ' + filepath);
      waitFileUnlock(filepath);
    }

    function startWatch(dirs) {
      return dirs.map(function(dir){
        return fs.watch(dir, function(e, p){ onDirChange(e, p, dir); });
      });
    }

    function restart(){
      if( restartTimer ) clearTimeout(restartTimer);
      
      restartTimer = setTimeout(function(){
        done();
        grunt.task.run([taskName]);
        executedThisTime = false;
        restartTimer = null;
      }, 2000);
    }

    function storeChecksum(dirs) {
      var memo = {},
          dirs = _.isArray(dirs) ? dirs : [dirs];
      
      dirs = dirs.map(function(dir){
        return dir.replace(/\/$/, '');
      });

      grunt.file.expand(dirs).map( function(path){
        var stat = fs.statSync( path ),
            hash = crypto.createHash('md5', path);

        if( stat.isDirectory() ) return;

        try {
          hash = require('crypto').createHash('md5').update( fs.readFileSync( path ) ).digest('hex');
          memo[ path ] = hash;
          grunt.verbose.ok(path + " -> " + hash);
        } catch(e) {
          grunt.log.error(e.message);
        }
      });
      return memo;
    }

    function closeWatch(watchers) {
      watchers.forEach(function(watcher){
        watcher.close();
      });
      return (watchers = []);
    }

    var waitTryCount = 0;
    function waitFileUnlock(filepath){
      var checksum, content = false, flg = true, isLocked = false;
      waitTryCount++;

      try {
        content = fs.readFileSync(filepath);
      } catch (e) {
        isLocked = true;
      }

      if(!isLocked || waitTryCount > 50) {
        if ( options.checksum && content ) {
          checksum = require('crypto').createHash('md5').update(content).digest('hex');

          grunt.verbose.ok(dict[filepath] +"  "+ checksum);

          flg = typeof dict[filepath] == "undefined" || dict[filepath] !== checksum;
          dict[filepath] = checksum;
        }
        if ( flg ) {
          var fp = path.join(options.dest|| '', filepath || '');
          grunt.file.copy(filepath, fp);
          grunt.log.ok('file copied to '+fp.blue);
        }

        return restart();

      } else {
        grunt.verbose.writeln('Waiting for file to unlock (' + waitTryCount + '): ' + filepath);
        clearTimeout(timerid);
        timerid = setTimeout(waitFileUnlock, 20);
      }
    }

    if( firstExecute ) {
      closeWatch(watchers);
      watchers = startWatch( grunt.file.expand(options.dirs) );

      firstExecute = false;
      if(options.checksum)  dict = storeChecksum( options.dirs );
      if(options.watchTask || options.watchtask)  done();
    }

  });
}
