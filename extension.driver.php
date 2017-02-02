<?php

Class extension_embeddable_page extends Extension
{

    public function getSubscribedDelegates()
    {
        return array(

            array('page' => '/frontend/',
                  'delegate' => 'FrontendPreRenderHeaders',
                  'callback' => 'frontendPreRenderHeaders',
            ),

            array('page' => '/blueprints/pages/',
                  'delegate' => 'AppendPageContent',
                  'callback' => '__appendType',
            )
            
        );
    }

    /**
     * allow to be placed in an iframe
     */
    public function frontendPreRenderHeaders($context) {
        $frontendPage = Frontend::instance()->Page();
        $pageTypes = $frontendPage->Params()['page-types'];
        
        if (in_array('embeddable', $pageTypes)) {
            $frontendPage->removeHeaderFromPage('X-Frame-Options');
            $frontendPage->removeHeaderFromPage('Access-Control-Allow-Origin');
            $frontendPage->addHeaderToPage('X-Frame-Options','ALLOWALL');
            $frontendPage->addHeaderToPage('Access-Control-Allow-Origin','*');
        }

    }

    /**
     * Append type for embeddable pages to page editor.
     *
     * @param array $context
     *  delegate context
     */
    public function __appendType($context)
    {
        // Find page types
        $elements = $context['form']->getChildren();
        $fieldset = $elements[0]->getChildren();
        $group = $fieldset[2]->getChildren();
        $div = $group[1]->getChildren();
        $types = $div[2]->getChildren();

        // Search for existing embeddable type
        $flag = false;

        foreach ($types as $type) {

            if ($type->getValue() === 'embeddable') {

                $flag = true;
            }
        }

        // Append embeddable type
        if ($flag === false) {

            $mode = new XMLElement('li', 'embeddable');
            $div[2]->appendChild($mode);
        }
    }

}
