<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>SB Admin 2 - Bootstrap Admin Theme</title>

        <!-- Bootstrap Core CSS -->
        <link href="/vendor/abtranslator/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

        <!-- MetisMenu CSS -->
        <link href="/vendor/abtranslator/vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

        <!-- DataTables CSS -->
        <link rel="stylesheet" type="text/css" href="/vendor/abtranslator/datatables/datatables.min.css"/>
        <!--<link href="/vendor/abtranslator/vendor/datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">-->

        <!-- DataTables Responsive CSS -->
        <!--<link href="/vendor/abtranslator/vendor/datatables-responsive/dataTables.responsive.css" rel="stylesheet">-->
        
        <!-- Custom CSS -->
        <link href="/vendor/abtranslator/dist/css/sb-admin-2.css" rel="stylesheet">

        <!-- Morris Charts CSS -->
        <link href="/vendor/abtranslator/vendor/morrisjs/morris.css" rel="stylesheet">

        <!-- Custom Fonts -->
        <link href="/vendor/abtranslator/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

        <!-- Flag Icon CSS -->
        <link href="/vendor/abtranslator/vendor/flag-icon-css/css/flag-icon.min.css" rel="stylesheet" type="text/css">

        <!-- Common CSS -->
        <link href="/vendor/abtranslator/css/common.css" rel="stylesheet">
        
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        
    </head>

    <body>
        @inject('appService', 'AB\Laravel\Translator\Services\AppService')
        
        <div id="wrapper">
            
            <!-- Navigation -->
            <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="{{ route('translator_index') }}">SB Admin v2.0</a>
                </div>
                <!-- /.navbar-header -->

                <!--@ include('ABTranslator::Common.NavbarHeader')-->
                <!-- /.navbar-top-links -->

                @include('ABTranslator::Common.NavbarSidebar')
                <!-- /.navbar-static-side -->
            </nav>

            <div id="page-wrapper">
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">{{ $title }}</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                
                @yield('translator_content')
            </div>
            <!-- /#page-wrapper -->

        </div>
        <!-- /#wrapper -->

        <!-- jQuery -->
        <script src="/vendor/abtranslator/vendor/jquery/jquery.min.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="/vendor/abtranslator/vendor/bootstrap/js/bootstrap.min.js"></script>

        <!-- Metis Menu Plugin JavaScript -->
        <script src="/vendor/abtranslator/vendor/metisMenu/metisMenu.min.js"></script>

        <!-- Morris Charts JavaScript -->
        <script src="/vendor/abtranslator/vendor/raphael/raphael.min.js"></script>
        <script src="/vendor/abtranslator/vendor/morrisjs/morris.min.js"></script>
        <script src="/vendor/abtranslator/data/morris-data.js"></script>
        
        <!-- DataTables JavaScript -->
        <script src="/vendor/abtranslator/datatables/datatables.min.js"></script>
        <!--<script src="/vendor/abtranslator/vendor/datatables/js/jquery.dataTables.min.js"></script>-->
        <!--<script src="/vendor/abtranslator/vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>-->
        <!--<script src="/vendor/abtranslator/vendor/datatables-responsive/dataTables.responsive.js"></script>-->
        

        <!-- Custom Theme JavaScript -->
        <script src="/vendor/abtranslator/dist/js/sb-admin-2.js"></script>

        @stack('translator_javascripts')
    </body>
    
</html>