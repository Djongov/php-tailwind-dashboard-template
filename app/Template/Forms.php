<?php

namespace Template;

class Forms
{
    public static function render(array $options, $confirm = false, $theme = 'amber')
    {
        $html = '';
        $formClass = 'generic-form';
        $theme = (isset($options['theme'])) ? $options['theme'] : $theme;
        if ($confirm) {
            $formClass .= ' confirm';
        }
        $html .= '<div class="m-4">';
            $html .= '<form class="' . $formClass . ' mb-4" action="' . $options['action'] . '">';
                foreach($options['inputs'] as $formType => $formArray) {
                    foreach ($formArray as $index => $metaArray) {
                        if ($formType === 'input') {
                            if (isset($metaArray['disabled']) && $metaArray['disabled']) {
                                $disabled = 'disabled ';
                            } else {
                                $disabled = '';
                            }
                            if (isset($metaArray['required']) && $metaArray['required']) {
                                $required = 'required ';
                            } else {
                                $required = '';
                            }
                            $placeholder = '';
                            if (isset($metaArray['input_placeholder'])) {
                                $placeholder = $metaArray['input_placeholder'];
                            }
                            $html .= '<div class="mb-6">';
                                $html .= '<div class="my-2">';
                                    $html .= '<label for="' . $metaArray['input_name'] . '" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">' . $metaArray['label_name'] . '</label>';
                                    $html .= '<input type="' . $metaArray['input_type'] . '" name="' . $metaArray['input_name'] . '" class="text-sm bg-gray-100 appearance-none border-2 border-gray-100 rounded-lg py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-' . $theme . '-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-' . $theme . '-500 dark:focus:border-' . $theme . '-500" placeholder="' . $placeholder . '" ' . $required . ' ' . $disabled . '/>';
                                    $html .= (isset($metaArray['description'])) ? '<p class="mt-2 text-sm text-gray-500 dark:text-gray-400">' . $metaArray['description'] . '</p>' : null;
                                $html .= '</div>';
                            $html .= '</div>';
                        }
                    }
                }
                // Finish the form with the button
                $buttonWidth = 'w-64';
                if (isset($options['button-width'])) {
                    $buttonWidth = $options['button-width'];
                }
                $buttonHeight = 'h-18';
                if (isset($options['button-height'])) {
                    $buttonHeight = $options['button-height'];
                }
                $buttonPadding = 'ml-0';
                if (isset($options['button-padding'])) {
                    $buttonPadding = $options['button-padding'];
                }
                $buttonMargin = 'py-2 px-4';
                if (isset($options['button-margin'])) {
                    $buttonMargin = $options['button-margin'];
                }
                $html .= '
                <div class="mt-4">
                    <button type="submit" class="' . $buttonPadding . ' ' . $buttonMargin . ' bg-' . $theme . '-600 hover:bg-' . $theme . '-700 focus:ring-' . $theme . '-500 focus:ring-offset-' . $theme . '-200 text-white ' . $buttonWidth . ' ' . $buttonHeight . ' transition ease-in duration-200 text-center text-base font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-lg">
                        ' . $options['button'] . '
                    </button>
                </div>';
            $html .= '</form>';
        $html .= '</div>';
        return $html;
    }
}
