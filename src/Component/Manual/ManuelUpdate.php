<?php


namespace App\Component\Manual;


use App\Component\PostgreSQL\Connection\PostgreSQLConnection;

class ManuelUpdate
{
    public static function execute():void
    {
        $pgsql = PostgreSQLConnection::connection();
        self::updateCategories($pgsql);
        self::updateAttributes($pgsql);
    }

    private static function updateCategories(\PDO $psql):void
    {
        self::updateCategory($psql, 'board', "Film Estimate Board of National Organizations", "Audience recommendation by the Board of National Organizations. Information sometimes gathered in the PCA file.");
        self::updateCategory($psql, 'censorship', "Censored content (Production Code Administration)", "Specific film content identified as problematic by the PCA during the production process. (Several themes sometimes for one film and describes the film for which there are data on the first verdict by the PCA).");
        self::updateCategory($psql, 'harrison', "Harrison's Reports", "Audience recommendation published in Harrison's Reports. Information sometimes gathered in the PCA file and available in the journal or the anthology 
Harrison's Reports and Film Reviews. See https://archive.org.");
        self::updateCategory($psql, 'legion', "Legion of Decency", "Film rating by the Catholic Legion of Decency, starting in 1933. Cf. Motion Pictures Classified by National Legion of Decency, Feb 1936-Oct. 1959 (New York: National Legion of Decency, 1959).");
        self::updateCategory($psql,'protestant', "Protestant Motion Picture Council");
        self::updateCategory($psql, 'state', "Countries and US States where the film was censored", "Countries and US States where the film was at least partially censored. This goes from minor cuts to stronger issues. Information available in the PCA file of the film.");
        self::updateCategory($psql, 'verdict', "PCA verdict", "Production Code Administration verdict on the first version of the script submitted by the producer. These data come out of the PCA collection at the Margaret Herrick Library (physical and digital). The phrasings are those usually used in the correspondence and are those found in the fist letter from the PCA to the studio. Of course the films first viewed as \"unacceptable\" were later made acceptable in the production process.");
    }

    private static function updateAttributes(\PDO $psql):void
    {
        // censorships
        self::updateAttribute($psql,'costumes-specific', "censorship", "The PCA forbids a specific costume, usually based on its photography sent by the studio.");
        self::updateAttribute($psql,'costumes-standard', "censorship", "Usual warning given to any producer about revealing costumes and reaffirmation of the necessity of being cautious.");
        self::updateAttribute($psql,'costumes-standard', "censorship", "Usual warning given to any producer about revealing costumes and reaffirmation of the necessity of being cautious.");
        self::updateAttribute($psql, 'dance', "censorship", "Choreographic contents are censored, because of suggestive moves. This concerns a lot of numbers based on burlesque dancing (fan dances in particular).");
        self::updateAttribute($psql,'dialogue', "censorship","Some lines have to be rewritten, usually because of profanity or double meaning.");
        self::updateAttribute($psql,'ethnic representation', "censorship","The PCA warns the studio about the accents, or the image given of ethnic communities or foreign populations.");
        self::updateAttribute($psql,'forbidden song', "censorship","A song is fully forbidden, or has to be entirely rewritten.");
        self::updateAttribute($psql,'lyrics', "censorship","Specific rewriting of some lines of a song.");
        self::updateAttribute($psql,'lyrics-unsignificant', "censorship","Usual criticism on the lyrics : sexual innuendo or possible double entendre, coarse language.");
        self::updateAttribute($psql,'narrative-major problem', "censorship","The script has to be modified and rewritten because of a major narrative issue (prostitution, religion, adultery, crime, the image given of institutions, alcoholism...).");
        self::updateAttribute($psql,'narrative-minor problem', "censorship","Usual remarks on the cautious treatment of alcohol drinking, gambling, toilets, representation of marriage. This implies minor cuts in the script.");
        self::updateAttribute($psql,'other', "censorship","Includes inappropriate gestures (pat on the botton, \"razzberry sound\"), the treatment of animals, playing with fire (smoking in bed...), advertising...");
        self::updateAttribute($psql,'"sex perversion"', "censorship","What the PCA refers to as \"sex perversion\", which means suggested or implied homosexuality.");

        // legion
        self::updateAttribute($psql,'A1', "legion", "Suitable for all audiences");
        self::updateAttribute($psql,'A2', "legion", "Suitable for adults (after the introduction of A3: suitable for adults and adolescents)");
        self::updateAttribute($psql,'A3', "legion", "Suitable for adults only");
        self::updateAttribute($psql,'B', "legion", "Morally objectionable in part");
        self::updateAttribute($psql,'C', "legion", "Condemned by the Legion");
        self::updateAttribute($psql,'NA', "legion", "Non applicable (for example is the film was produced before 1933)");
    }

    private static function updateCategory(\PDO $psql, string $code, string $title, string $description = null):void
    {
        $rsl = $psql->prepare('UPDATE category SET  title =:title,  description=:description, updated_at = NOW() WHERE code = :code');
        $rsl->execute([
            'title' => $title,
            'description' => $description,
            'code' => $code
            ]
        );
    }

    private static function updateAttribute(\PDO $psql, string $title, string $categoryCode, string $description = null):void
    {
        $rsl = $psql->prepare('UPDATE attribute SET  title =:title,  description=:description, updated_at = NOW() WHERE category_id IN (SELECT id FROM category WHERE code = :code) AND title =:title');
        $rsl->execute([
                'title' => $title,
                'description' => $description,
                'code' => $categoryCode
            ]
        );
    }
}
