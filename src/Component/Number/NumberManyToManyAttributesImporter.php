<?php


namespace App\Component\Number;


use App\Component\Migration\ImportAttributes;

class NumberManyToManyAttributesImporter
{
    public static function import():void
    {
        // import complet_options
        ImportAttributes::importRelationsForExistingAttributes('number_has_completoptions', 'number_attribute', 'complet_options', 'number', 'attribute', 'number_id', 'completoptions_id',  1000);

        // import dancecontent
        ImportAttributes::importRelationsForExistingAttributes('number_has_dancecontent', 'number_attribute', 'dance_content', 'number', 'attribute', 'number_id', 'dancecontent_id',  1000);

        // import dancemble
        ImportAttributes::importRelationsForExistingAttributes('number_has_dancemble', 'number_attribute', 'dancemble', 'number', 'attribute', 'number_id', 'dancemble_id',  1000);

        // import dancesubgenre
        ImportAttributes::importRelationsForExistingAttributes('number_has_dancesubgenre', 'number_attribute', 'dance_subgenre', 'number', 'attribute', 'number_id', 'dancesubgenre_id',  1000);

        // dancingtype
        ImportAttributes::importRelationsForExistingAttributes('number_has_dancingtype', 'number_attribute', 'dancing_type', 'number', 'attribute', 'number_id', 'dancingtype_id',  1000);

        // diegeticplace
        ImportAttributes::importRelationsForExistingAttributes('number_has_diegeticplace', 'number_attribute', 'diegetic_place_thesaurus', 'number', 'attribute', 'number_id', 'diegetic_place_thesaurus_id',  1000);

        // exoticismthesaurus
        ImportAttributes::importRelationsForExistingAttributes('number_has_exoticismthesaurus', 'number_attribute', 'exoticism_thesaurus', 'number', 'attribute', 'number_id', 'exoticism_thesaurus_id',  1000);

        // genre => topic
        ImportAttributes::importRelationsForExistingAttributes('number_has_genre', 'number_attribute', 'genre', 'number', 'attribute', 'number_id', 'genre_id',  1000);

        // imaginary
        ImportAttributes::importRelationsForExistingAttributes('number_has_imaginary', 'number_attribute', 'imaginary', 'number', 'attribute', 'number_id', 'imaginary_id',  1000);

        // musensemble
        ImportAttributes::importRelationsForExistingAttributes('number_has_musensemble', 'number_attribute', 'musensemble', 'number', 'attribute', 'number_id', 'musensemble_id',  1000);

        // musicalthesaurus
        ImportAttributes::importRelationsForExistingAttributes('number_has_musicalthesaurus', 'number_attribute', 'musical_thesaurus', 'number', 'attribute', 'number_id', 'musical_thesaurus_id',  1000);

        // quotationthesaurus
        ImportAttributes::importRelationsForExistingAttributes('number_has_quotationthesaurus', 'number_attribute', 'quotation_thesaurus', 'number', 'attribute', 'number_id', 'quotation_id',  1000);

        // source
        ImportAttributes::importRelationsForExistingAttributes('number_has_source', 'number_attribute', 'source_thesaurus', 'number', 'attribute', 'number_id', 'source_thesaurus_id',  1000);

        // stereotype
        ImportAttributes::importRelationsForExistingAttributes('number_has_stereotype', 'number_attribute', 'stereotype_thesaurus', 'number', 'attribute', 'number_id', 'stereotype_id',  1000);
    }
}