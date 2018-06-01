@extends('Translator::layout')

@section('translator_content')
<!-- /.row -->
<div class="row">
    <div class="col-lg-4 col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-comments fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge">{{ $messagesCount }}</div>
                        <div>{{ trans('translator::messages.messages') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="panel panel-green">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-comments fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge">{{ $untranslatedMessagesCount }}</div>
                        <div>{{ trans('translator::messages.untranslated_messages') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="panel panel-yellow">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-comments fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge">{{ $languagesCount }}</div>
                        <div>{{ trans('translator::messages.languages') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-list-ul fa-fw"></i> {{ trans('translator::messages.languages') }}
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="list-group">

                    @foreach ($languages as $language)
                    <a href="{{ route('translator_language', ['language' => $language['locale']]) }}" class="list-group-item">
                        <i class="flag-icon flag-icon-{{ $language['country'] }}"></i> {{ $language['name'] }}
                        <span class="pull-right text-muted small"><em>{{ trans_choice('translator::messages.all_messages_count', $language['messages'], ['value' => $language['messages']]) }}, {{ trans_choice('translator::messages.untranslated_messages_count', $language['untranslated'], ['value' => $language['untranslated']]) }}</em></span>
                    </a>
                    @endforeach
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
</div>
<!-- /.row -->
@endsection