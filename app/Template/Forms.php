<?php

namespace Template;

class Forms
{
    public static function render(array $options, $theme = COLOR_SCHEME)
    {
        $html = '';
        $formClass = 'generic-form';
        $theme = (isset($options['theme'])) ? $options['theme'] : $theme;
        if (isset($options['confirm']) && $options['confirm']) {
            $formClass .= ' confirm';
        }
        $confirmData = (isset($options['confirmText'])) ? 'data-confirm="' . $options['confirmText'] . '"' : null;

        $reloadOnSubmitData = 'data-reload="' . (isset($options['reloadOnSubmit']) && $options['reloadOnSubmit'] ? 'true' : 'false') . '"';

        $resultType = (isset($options['resultType'])) ? 'data-result="' . $options['resultType'] . '"' : null;
        
        $html .= '<div class="m-4">';
            $html .= '<form class="' . $formClass . ' mb-4" action="' . $options['action'] . '"' . $reloadOnSubmitData . $confirmData . $resultType . '>';
                foreach($options['inputs'] as $formType => $formArray) {
                    foreach ($formArray as $metaArray) {
                        // Setup the meta such as readonly, disabled, required
                        $disabled = (isset($metaArray['disabled']) && $metaArray['disabled']) ? 'disabled' : '';
                        $required = (isset($metaArray['required']) && $metaArray['required']) ? 'required' : '';
                        $readonly = (isset($metaArray['readonly']) && $metaArray['readonly']) ? 'readonly' : '';
                        $checked = (isset($metaArray['checked']) && $metaArray['checked']) ? 'checked' : '';
                        $placeholder = isset($metaArray['placeholder']) ? 'placeholder="' . $metaArray['placeholder'] . '"': '';
                        // Inputs such as text, email
                        if ($formType === 'input') {
                            $html .= '<div class="mb-6">';
                                $html .= '<div class="my-2">';
                                    $html .= '<label for="' . $metaArray['name'] . '" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">' . $metaArray['label_name'] . '</label>';
                                    $html .= '<input type="' . $metaArray['input_type'] . '" name="' . $metaArray['name'] . '" class="text-sm bg-gray-100 appearance-none border-2 border-gray-100 rounded-lg py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-' . $theme . '-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-' . $theme . '-500 dark:focus:border-' . $theme . '-500" ' . $placeholder . ' ' . $required . ' ' . $disabled . ' ' . $readonly . ' />';
                                    $html .= (isset($metaArray['description'])) ? '<p class="mt-2 text-sm text-gray-500 dark:text-gray-400">' . $metaArray['description'] . '</p>' : null;
                                $html .= '</div>';
                            $html .= '</div>';
                        }
                        // Inputs such as checkbox
                        if ($formType === 'checkbox') {
                            $html .= '<div class="mt-2">';
                            $html .= '<div class="flex items-center mb-4">';
                            $html .= '<input name="' . $metaArray['name'] . '" type="checkbox" value="' . $metaArray['value'] . '" class="w-4 h-4 text-' . $theme . '-600 bg-gray-100 rounded border-gray-300 focus:ring-' . $theme . '-500 dark:focus:ring-' . $theme . '-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" ' . $checked . ' ' . $required . ' ' . $disabled . ' ' . $readonly . '/>';
                            $html .= '<label for="' . $metaArray['name'] . '" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">' . $metaArray['label_name'] . '';
                            $html .= '<i data-popover-target="' . $metaArray['name'] . '-info" class="cursor-pointer ml-1 rounded-full border border-gray-300">i<div data-popover id="' . $metaArray['name'] . '-info" role="tooltip" class="absolute z-10 invisible inline-block w-64 text-sm font-light text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 dark:text-gray-400 dark:border-gray-600 dark:bg-gray-800">
                                            <div class="px-3 py-2">
                                                <p>' . $metaArray['description'] . '</p>
                                            </div>
                                        </div>
                                    </i>
                                    </label>';
                            $html .= '</div>';
                            $html .= '</div>';
                        }
                        if ($formType === 'select') {
                            $html .= '<div class="mb-6">';
                            $html .= '<div class="my-2">';
                            $html .= '<label for="' . $metaArray['name'] . '" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">' . $metaArray['label_name'] . '</label>';
                            $html .= '<select name="' . $metaArray['name'] . '" class="' . Html::selectInputClasses($theme) . '">';
                            $html .= (isset($metaArray['description'])) ? '<p class="mt-2 text-sm text-gray-500 dark:text-gray-400">' . $metaArray['description'] . '</p>' : null;
                            $selectedOption = $metaArray['selected_option'] ?? null;
                            foreach ($metaArray['options'] as $name => $value) {
                                if ($selectedOption !== null && $selectedOption === $name) {
                                    $html .= '<option value="' . $value . '" selected>' . $name . '</option>';
                                } else {
                                    $html .= '<option value="' . $value . '">' . $name . '</option>';
                                }
                            }
                            $html .= '</select>';
                            $html .= '</div>';
                            $html .= '</div>';
                        }
                        // Hidden inputs
                        if ($formType === 'hidden') {
                            $html .= '<input type="hidden" name="' . $metaArray['name'] . '" value="' . $metaArray['value'] . '" />';
                        }
                    }
                }
                if ($options['buttonSize'] === 'big') {
                    $customClasses = 'ml-0 py-4 px-8';
                } elseif ($options['buttonSize'] === 'medium') {
                    $customClasses = 'ml-0 py-3 px-6';
                } elseif ($options['buttonSize'] === 'small') {
                    $customClasses = 'ml-0 py-2 px-4';
                }
                $html .= '
                <div class="mt-4">
                    <button type="submit" class="' . $customClasses . ' bg-' . $theme . '-600 hover:bg-' . $theme . '-700 focus:ring-' . $theme . '-500 focus:ring-offset-' . $theme . '-200 text-white transition ease-in duration-200 text-center text-base font-semibold shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-lg">
                        ' . $options['button'] . '
                    </button>
                </div>';
            $html .= '</form>';
        $html .= '</div>';
        return $html;
    }
}
