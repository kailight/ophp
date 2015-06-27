<?php




$code11_php = <<<HEREDOC
\$string = "Arguments order is hard to remember";
echo str_replace(\$string,'hard','easy');
// >> easy
HEREDOC;

$code11_ophp = <<<HEREDOC
\$string = "Arguments order is hard to remember";
echo new iString(\$string)->replace('hard','easy');
// Arguments order is easy to remember
HEREDOC;



$code12_php = <<<HEREDOC
\$array = Array('Arguments','order','is','hard','to','remember');
\$array = array_flip(\$array);
var_dump( key_exists(\$array,'hard') );
// Warning: key_exists() expects parameter 2 to be array, string given...
// >> null
HEREDOC;

$code12_ophp = <<<HEREDOC
\$array = new Array('Arguments','order','is','hard','to','remember');
echo new iArray(\$array)->flip()->keyExists('hard');
// >> true
HEREDOC;



$code13_php = <<<HEREDOC

\$array = Array('PHP','love','I');
\$array = reverse(\$array);
var_dump( \$array );
// Fatal error: Call to undefined function reverse()
// >> null
HEREDOC;

$code13_ophp = <<<HEREDOC
\$iArray = new iArray('PHP','love','I');
echo \$iArray->reverse()->implode(' ');
// I love PHP
HEREDOC;


$code2_php1 = <<<HEREDOC
\$string = "Remove extra   white  space from    string";
\$array  = explode(' ',\$string);
\$array  = array_walk(\$array,'trim');
echo implode(' ',\$string);
// >> Remove extra white space from string
HEREDOC;

$code2_php2 = <<<HEREDOC
\$string = "Remove extra   white  space from    string";
\$array  = implode(' ', array_walk( explode(' ',\$string), 'trim');
// >> Remove extra white space from string
HEREDOC;

$code2_ophp = <<<HEREDOC
\$string = "Remove extra   white  space from    string";
echo new iString(\$string)->explode(' ')->walk('trim')->implode(' ');
// >> Remove extra white space from string
HEREDOC;




$code3_php1 = <<<HEREDOC
\$contents = file_get_contents("newfile.txt");
\$files = scandir
file_put_contents(\$myfile,\$contents."Extra data");
HEREDOC;

$code3_php2 = <<<HEREDOC
file_put_contents( "newfile.txt", file_get_contents("newfile.txt")."Extra data");
HEREDOC;

$code3_ophp = <<<HEREDOC
\$myfile = iFile("newfile.txt")->appendContents("Sample data"));
HEREDOC;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>O'PHP - OO PHP :: Object-Oriented PHP</title>

    <!-- CSS -->
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="assets/css/font-awesome.min.css" rel="stylesheet" media="screen">
    <link href="assets/css/simple-line-icons.css" rel="stylesheet" media="screen">
    <link href="assets/css/animate.css" rel="stylesheet">

    <!-- Custom styles CSS -->
    <link href="assets/less/sandbox.css" rel="stylesheet" media="screen">
    <link href="assets/less/footer.css" rel="stylesheet" media="screen">

    <script src="assets/js/modernizr.custom.js"></script>
    <link rel="stylesheet" href="/bower_components/highlightjs/styles/default.css">
    <script src="/bower_components/highlightjs/highlight.pack.js"></script>
    <script>hljs.initHighlightingOnLoad();</script>

</head>
<body>

<?php include 'navigation.php' ?>

<section id="intro">
    <div class="logo">

    </div>
<h1>O'PHP - OO PHP*</h1>

<h3>* - Very Alpha version**</h3>
<h4>** - care it <a class="bites" href="#">bites</a>!</h4>
</section>



<section id="samples">
    <div class="container">

        <div class="row">
            <div class="col-sm-6 php-code"><h2 class="php">PHP</h2></div>
            <div class="col-sm-6 php-code"><h2 class="ophp">iPHP</h2></div>
        </div>

        <h3>More intuitive, error-proof</h3>

        <h4>Arguments order</h4>

        <div class="row">
            <div class="col-sm-6 php">
                <pre><code class="php"><?= $code11_php ?></code></pre>
            </div>
            <div class="col-sm-6 ophp">
                <pre><code class="php"><?= $code11_ophp ?></code>
                </pre></div>
        </div>

        <div class="row">
            <div class="col-sm-6 php">
                <pre><code class="php"><?= $code12_php ?></code></pre>
            </div>
            <div class="col-sm-6 ophp">
                <pre><code class="php"><?= $code12_ophp ?></code>
                </pre>
            </div>
        </div>

        <h4>Function names</h4>

        <div class="row">
            <div class="col-sm-6 php">
                <pre><code class="php"><?= $code13_php ?></code></pre>
            </div>
            <div class="col-sm-6 ophp">
                <pre><code class="php"><?= $code13_ophp ?></code>
                </pre></div>
        </div>

        <div class="row">
            <div class="col-sm-6 php">
                <pre><code class="php"><?= $code14_php ?></code></pre>
            </div>
            <div class="col-sm-6 ophp">
                <pre><code class="php"><?= $code14_ophp ?></code>
                </pre>
            </div>
        </div>

    </div><!-- .contaier -->

    <div class="container">

        <h3>Better readability / shorter code</h3>


        <div class="row">
            <div class="col-sm-6 php">
                <pre><code class="php"><?= $code2_php1 ?></code></pre>
                or
                <pre><code class="php"><?= $code2_php2 ?></code></pre>
            </div>
            <div class="col-sm-6 php">
                <pre><code class="php"><?= $code2_ophp ?></code></pre>
            </div>
        </div>

    </div><!-- .contaier -->

    <div class="container">

        <h3>More logical</h3>

        <div class="row">
            <div class="col-sm-6">
                <pre><code class="php"><?= $code3_php1 ?></code></pre>
                or
                <pre><code class="php"><?= $code3_php2 ?></code></pre>
            </div>
            <div class="col-sm-6">
                <pre><code class="php"><?= $code3_ophp ?></code></pre>
            </div>
        </div>

    </div><!-- .contaier -->
</section>

<section id="why" class="block-gray">
    <div class="container">

        <h2>Why</h2>
        <hr>
        <h3>Why would I do that!</h3>

        <div class="row">
            <div class="col-sm-12">
<p>During my career, I often have to develop in PHP, and got tired of being unable to chain or having to count nested (((parenthesis))).</p>
<p>I have quickly checked the web if there is such a set of classes that is popular, found none.</p>
<p>So, rather than trying to find and contribute to similar project, I decided to create one and manage.</p>
            </div>
        </div>

        <h3>Why no one else would do that?</h3>

        <div class="row">
            <div class="col-sm-12">
                <p>I have no idea why this set of classes isn't popular on web.</p>
                <p>
                    I guess people that start server-side in PHP, switch to either
                    <a href="#Codeigniter">some</a> <a href="#Codeigniter">solid</a> <a href="#Codeigniter">framework</a> or
                    <a href="https://www.ruby-lang.org/">some</a> <a href="https://www.ruby-lang.org/">other</a> <a href="https://www.ruby-lang.org/">language</a>,
                    quite soon, leaving PHP in the past.
                </p>
            </div>
        </div>

        <h3>Who is this for</h3>
        <div class="row">
            <div class="col-sm-12">
                <p>This set of classes aimed for just anyone.</p>
                <p>
                    However I guess it's more suited for those that are beginning to learn PHP, and want to learn Object-Oriented programming.
                </p>
            </div>
        </div>

            </div>
        </div>

    </div><!-- .contaier -->
</section>


<?php include 'footer.php' ?>


<!-- Javascript files -->

<script src="assets/js/jquery-1.11.1.min.js"></script>
<script src="assets/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/js/ophp.js"></script>

</body>
</html>