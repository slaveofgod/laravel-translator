<?php

namespace Translator\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Symfony\Component\Finder\Finder;
use Translator\Services\TranslatorService;

class DefaultController extends BaseController
{
    /**
     *
     * @var string
     */
    private $resource_path;
    
    /**
     *
     * @var string
     */
    private $template_path;
    
    /**
     *
     * @var string
     */
    private $prefix = '__,@lang,trans_choice,@choice,__ab,@lang_ab,trans_choice_ab,@choice_ab';
    
    
    
    public function __construct()
    {
        // Define resource paths
        $this->resource_path = resource_path();
        
        // Define resource path
        $this->template_path = $this->resource_path . '/views';
    }
    
    public function index(Request $request)
    {
        $languages = [];
        
        $finder = new Finder();

        // Files
        foreach ($finder->files()->in(resource_path('lang'))->depth('== 0') as $file) {
            $languages[$file->getBasename('.' . $file->getExtension())] = [];
        }
        
        // Directories
        foreach ($finder->directories()->in(resource_path('lang'))->exclude('backup') as $file) {
            $languages[$file->getRelativePathname()] = [];
        }
        
        $untranslatedMessagesCount = 0;
        $messagesCount = 0;
        foreach ($languages as $key => $value) {
            $translator = new TranslatorService(
                $key,
                $this->resource_path,
                $this->template_path,
                $this->prefix
            );
            
            $languages[$key]['locale'] = $key;
            $languages[$key]['untranslated'] = $translator->getUntranslatedMessagesCount();
            $languages[$key]['messages'] = $translator->getMessagesCount();
            $languages[$key]['name'] = \Locale::getDisplayLanguage($key, \App::getLocale());
            $languages[$key]['country'] = locale_country($key);
            
            $untranslatedMessagesCount += $languages[$key]['untranslated'];
            $messagesCount += $languages[$key]['messages'];
        }
        
        return view('Translator::Default/Index', [
            'title' => \Lang::get('translator::messages.dashboard'),
            'languages' => $languages,
            'messagesCount' => $messagesCount,
            'untranslatedMessagesCount' => $untranslatedMessagesCount,
            'languagesCount' => count($languages)
        ]);
    }
    
    public function language(Request $request, $language)
    {
        $translator = new TranslatorService(
            $language,
            $this->resource_path,
            $this->template_path,
            $this->prefix
        );
        
        $resources = [];
        foreach ($translator->getResources() as $resource) {
            $messages = [];
            foreach ($resource->getMessages() as $key => $value) {
                if (is_array($value)) {
                    continue;
                }
                
                $messages[] = [
                    'DT_RowId' => md5($resource->getRelativePathname()) . ',' . md5($key),
                    'original' => $key,
                    'translation' => $value
                ];
            }
            
            $resources[] = [
                'pathname' => $resource->getRelativePathname(),
                'messages' => $messages,
                'messagesJSON' => json_encode($messages, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE)
            ];
        }
        
        return view('Translator::Default/Language', [
            'title' => \Lang::get('translator::messages.language_translation_messages', ['value' => \Locale::getDisplayLanguage($language, \App::getLocale())]),
            'resources' => $resources,
            'language' => $language
        ]);
    }
    
    /**
     * 
     * @param Request $request
     * @param string $language
     * 
     * @return array
     */
    public function edit(Request $request, $language)
    {
        $translator = new TranslatorService(
            $language,
            $this->resource_path,
            $this->template_path,
            $this->prefix
        );
        
        $data = null;
        foreach ($request->get('data') as $dataHesh => $dataValue) {
            list($filePathHesh, $messageHesh) = explode(',', $dataHesh);
            
            $resource = $translator->getResourceByHesh($filePathHesh);
            if ($resource) {
                $message = $resource->getMessageByHesh($messageHesh);
                if ($message) {
                    if (true === $resource->updateMessage($message['original'], $dataValue['translation'])) {
                        $resource->save();
                    }
                    
                    $data[] = [
                        'DT_RowId' => $message['hesh'],
                        'original' => $message['original'],
                        'translation' => $dataValue['translation']
                    ];
                }
            }
        }
        
        return ['data' => $data];
    }
}