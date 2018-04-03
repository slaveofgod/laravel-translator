{{ trans_choice('{1} :value minute ago|[2,*] :value minutes ago', 5, ['value' => 5]) }}<br />

{{ trans_choice(  '{1} :value day ago|[2,*] :value days ago'   , 5, ['value' => 5]) }}<br />

{{ __('Welcome, :name', ['name' => 'dayle']) }}<br />

@lang('Hi, :name', ['name' => 'dayle'])<br />

@lang("You'r welcome :name", ['name' => 'dayle'])<br />

{{ __("You'r welcome") }}<br />

{{ __('New Users') }}<br />

@lang('Table View: User')<br />

@choice('{1} :value minute ago|[2,*] :value minutes ago', 5, ['value' => 5])<br />