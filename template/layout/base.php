<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Framework Pédagogique MVC6</title>
    <?php echo $view->add_webpack_style('app'); ?>
  </head>
  <body>
  <?php // $view->dump($view->getFlash()) ?>
    <header id="masthead">
      <nav>
          <ul>
              <li><a href="<?= $view->path(''); ?>">Home</a></li>
          </ul>
      </nav>
    </header>

    <div class="container">
        <?= $content; ?>
    </div>

    <footer id="colophon">
        <div class="wrap">
            <p>MVC 6 - Framework Pédagogique.</p>
        </div>
    </footer>
  <?php echo $view->add_webpack_script('app'); ?>
  </body>
</html>
