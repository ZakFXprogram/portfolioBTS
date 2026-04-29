# Oral de présentation — Portfolio BTS SIO (option SLAM)

> Durée cible : **~10 minutes**
> Débit moyen : ~140 mots/minute → ~1 400 mots
> Structure : 1) Présentation personnelle ~2 min — 2) Compétences via 2 projets ~6 min — 3) Stages ~1 min 30 — 4) Conclusion ~30 s
>
> Les passages entre crochets `[…]` sont à personnaliser avant l'oral.

---

## 1. Présentation personnelle (≈ 2 minutes)

> *(Pose, sourire, regard jury — on prend son temps.)*

Bonjour Madame, Monsieur, et merci de me recevoir aujourd'hui pour la soutenance de mon portfolio de BTS SIO.

Je m'appelle **[Prénom Nom]**, j'ai **[âge]** ans, et je suis étudiant en deuxième année de BTS Services Informatiques aux Organisations, option **SLAM** — Solutions Logicielles et Applications Métiers — au lycée **[nom du lycée]**.

Avant d'arriver dans cette formation, j'ai suivi un parcours **[bac STMG / bac général spé NSI / bac pro SN — à adapter]**, qui m'a donné un premier contact avec l'informatique. Mais c'est vraiment au cours du BTS que j'ai construit une vraie posture de développeur : passer d'un état d'esprit « je fais marcher un programme » à un état d'esprit « je livre un service qui répond à un besoin métier, dans des délais, avec des contraintes, et en travaillant en équipe ».

Sur le plan personnel, je suis quelqu'un de **curieux et de méthodique**. J'aime comprendre comment les choses fonctionnent en profondeur — c'est probablement ce qui m'a poussé vers l'option SLAM plutôt que SISR : le code, c'est ce moment où une idée abstraite devient un produit concret qu'on peut tester, casser, améliorer. En dehors de la formation, je m'intéresse particulièrement à **[finance quantitative / web / cybersécurité — à adapter selon vos centres d'intérêt]** : j'ai d'ailleurs développé sur mon temps libre un outil personnel, **GEX (Gamma Exposure Financial Tool)**, qui agrège des données d'options pour visualiser l'exposition gamma des marchés. Ce projet m'a appris autant que la formation elle-même, parce qu'il n'y avait pas de cahier des charges et qu'il fallait définir le besoin, l'architecture et les priorités tout seul.

Sur le plan professionnel, j'ai eu la chance d'effectuer **deux stages dans le même groupe — La Banque Postale** — sur deux environnements très différents : d'abord un stage de **développement de tests automatisés** en Angular, Cypress et Java, puis un stage **mainframe** en COBOL, JCL et DB2 sur z/OS. J'y reviendrai en fin de présentation, mais ces deux expériences m'ont confirmé une chose : ce qui m'intéresse, c'est de livrer du code utilisé en production, pas seulement du code qui compile.

Le portfolio que je vais vous présenter aujourd'hui est lui-même un projet personnel : un site **PHP avec une architecture MVC maison**, hébergé sur mon GitHub, qui regroupe mes projets, mes compétences validées, mes stages, et une partie blog avec un fil RSS. Ce n'est pas juste une vitrine : c'est aussi une démonstration que je suis capable de concevoir une application web complète, de bout en bout, pour mon propre compte.

Je vais maintenant vous présenter mes compétences à travers **deux projets qui, à eux deux, couvrent l'intégralité des six blocs de compétences du référentiel BTS SIO**.

---

## 2. Les compétences via deux projets (≈ 6 minutes)

Pour cet oral, j'ai fait le choix volontaire de me concentrer sur **deux projets complémentaires** plutôt que de survoler les sept que contient mon portfolio. Ces deux projets sont les plus aboutis de ma formation, ce sont mes **deux ateliers professionnels de deuxième année (AP 3.1 et AP 3.2)**, et **à eux deux ils couvrent les six blocs de compétences du référentiel BTS SIO** :

- **Projet A — AP 3.1 « M2L Site Dynamique »** : projet à 3 développeurs sur 5 semaines, refonte d'un site institutionnel en application web dynamique PHP MVC.
- **Projet B — AP 3.2 « Aux Claviers Citoyens »** : projet en lien avec l'**armée**, consommation d'une **API REST sécurisée OAuth2** pour gérer des tournois — il complète A sur la sous-compétence **« Collecter, suivre et orienter des demandes »** que A ne valide pas, et il prouve ma capacité à intégrer un système tiers normé.

> *(Optionnel : montrer rapidement la matrice de compétences du portfolio à l'écran.)*

### 2.1 — Projet A : AP 3.1 — Refonte dynamique du site de la Maison des Ligues (≈ 3 min 30)

Le client est la **Maison des Ligues de Lorraine (M2L)**. Il disposait déjà d'un site statique, et la demande était claire : faire évoluer ce site vers une **application web dynamique en PHP objet**, avec des modules pour la gestion des ligues, des bulletins de salaire et des formations, le tout avec une **gestion fine des droits par rôle** (admin, RH, secrétaire, responsable formation, intervenant). Cinq semaines, trois développeurs, vingt heures effectives par étudiant.

Je vais maintenant dérouler **les six blocs de compétences** à travers ce projet, et expliquer **pourquoi** chaque sous-compétence avait du sens dans ce contexte — pas juste *comment* je l'ai validée, mais *pourquoi* elle était nécessaire.

#### Bloc 1 — Gérer le patrimoine informatique

- **Exploiter les référentiels, normes et standards** : nous avons imposé un modèle MVC, la validation HTML/CSS au W3C, des conventions de nommage strictes. **Pourquoi ?** Parce qu'un cadre normalisé est la condition pour qu'à trois développeurs on produise un code cohérent et maintenable par un quatrième dans six mois.
- **Vérifier la continuité du service** : gestion des droits par rôle. **Pourquoi ?** Parce que l'application manipule des contrats, des salaires et des données personnelles : une fuite due à un mauvais contrôle d'accès, ce n'est pas un bug, c'est une faille RGPD avec des conséquences juridiques.
- **Vérifier le respect des règles d'utilisation des ressources** : conformité RGPD explicite. **Pourquoi ?** Parce que la M2L manipule des données d'intervenants salariés et bénévoles ; la conformité n'est pas optionnelle, elle est légale.

#### Bloc 2 — Répondre aux incidents et aux demandes d'évolution

- **Traiter une demande applicative** et **traiter une demande d'évolution** : le client avait un site statique fonctionnel, on l'a fait évoluer vers une application dynamique. **Pourquoi ?** Parce que faire évoluer l'existant plutôt que tout réécrire respecte le travail déjà fait et limite le risque pour le client. Chaque module — ligues, contrats/bulletins, formations — répond à un besoin métier précis et différent.

#### Bloc 3 — Développer la présence en ligne de l'organisation

- **Valoriser l'image de l'organisation** : refonte visuelle homogène dans le respect du RGPD. **Pourquoi ?** Parce qu'un site dynamique modernisé renforce la crédibilité de la M2L vis-à-vis des ligues et des intervenants.
- **Référencer les services en ligne** : affichage des ligues, clubs et localisations. **Pourquoi ?** Parce que sans ce référencement, le site reste une plaquette sans valeur d'usage pour un visiteur qui cherche une activité.

#### Bloc 4 — Travailler en mode projet

- **Analyser les objectifs**, **planifier**, **évaluer les écarts** : Trello, MEA validé en première séance, suivi des écarts prévu/réel. **Pourquoi ?** Parce qu'à trois développeurs sur cinq semaines avec un cahier des charges riche, sans planification on découvre les retards à la fin — trop tard pour réagir. Comparer le prévu et le réel est ce qui transforme un planning en outil de pilotage.

#### Bloc 5 — Mettre à disposition un service informatique

- **Tests d'intégration et d'acceptation** : tests par profil, par fonctionnalité, par droit d'accès. **Pourquoi ?** Parce qu'une application qui gère des droits et des données sensibles ne peut pas être livrée sans recette.
- **Déployer le service** : livraison complète avec base de données. **Pourquoi ?** Parce que tant qu'une application n'est pas déployée avec sa base, elle reste un prototype sur ma machine.
- **Accompagner les utilisateurs** : livrable « liste des comptes login/mdp » pour chaque rôle. **Pourquoi ?** Parce qu'une application multi-rôles est inutilisable si l'utilisateur ne sait pas avec quel compte se connecter.

#### Bloc 6 — Organiser son développement professionnel

- **Mettre en place son environnement d'apprentissage** et **veille** : MVC en PHP objet et Git en branches étaient nouveaux pour moi. **Pourquoi ?** Parce que ces technologies évoluent vite et que sans veille active, on travaille avec des pratiques obsolètes et on se retrouve dépassé en entreprise.

Voilà pour le projet A. **Cinq blocs sur six couverts**, avec une vraie densité de sous-compétences. Il manque une sous-compétence du bloc 2 — *Collecter, suivre et orienter des demandes* — que je veux justifier : c'est là qu'intervient le second projet.

### 2.2 — Projet B : AP 3.2 — « Aux Claviers Citoyens » : application de gestion de tournois pour l'armée (≈ 1 min 30)

Le contexte est très différent du projet M2L et c'est ce qui m'a intéressé : il s'agit d'une **application qui consomme une API REST sécurisée par OAuth2 (Bearer token)**, pour gérer un cycle de tournois — équipes, matchs, scores — pour le compte de **l'armée**. Le contrat d'API était fourni : 5 modules (auth, utilisateurs, tournois, équipes, matchs), des endpoints précis, des formats JSON imposés.

Ce projet apporte deux choses que M2L ne couvrait pas et qui sont fondamentales pour mon profil :

- **Collecter, suivre et orienter des demandes** (bloc 2) : les besoins fonctionnels n'étaient pas tous figés au départ. **Échanger avec l'armée pour cadrer les zones grises** était indispensable. **Pourquoi cette sous-compétence est-elle validée ici et pas dans M2L ?** Parce que dans M2L on développait pour un cahier des charges complet et stable ; ici, j'ai dû **collecter activement des informations auprès du client final**, les suivre, les orienter vers les bonnes parties de l'application. C'est exactement le sens de la compétence.
- **Exploiter un référentiel imposé** dans une dimension nouvelle : un **contrat d'API tiers**. **Pourquoi ?** Parce que respecter strictement un contrat d'API (endpoints, format JSON, OAuth2 Bearer) n'est pas un choix mais la condition technique pour que l'application communique avec un système qu'on ne contrôle pas. Le moindre écart casse l'intégration. C'est un cas typique en entreprise : on s'intègre à des systèmes existants, on ne les redéfinit pas.

Et toutes les autres sous-compétences du référentiel — traitement de la demande applicative (CRUD complet tournois/équipes/matchs), planification Trello, tests d'intégration sur chaque endpoint, montée en compétence sur OAuth2 et la consommation d'API REST, veille technique sur la documentation d'API — sont également validées dans ce projet, ce qui me permet de **redoubler la validation** sur ce qu'on a vu dans M2L et de prouver que ces compétences ne sont pas ponctuelles, mais transférables d'un contexte à l'autre.

> **Synthèse compétences :** entre AP 3.1 et AP 3.2, **les six blocs et la majorité des sous-compétences du référentiel sont validés**, sur deux projets de natures très différentes — une application web complète en équipe de trois pour un acteur sportif associatif, et l'intégration d'une API REST sécurisée pour un client institutionnel exigeant.

---

## 3. Les stages (≈ 1 minute 30)

J'ai eu la chance d'effectuer mes deux stages dans le même groupe, **La Banque Postale**, ce qui m'a permis de construire un parcours cohérent et de revenir dans une équipe que je connaissais déjà.

### Stage 1 — Mai/Juin 2025 — Développeur d'applications Junior

**Contexte** : participation au développement et à l'**automatisation de tests logiciels** dans un environnement Agile/Scrum.

**Missions principales** :
- Développement de **tests de non-régression bout en bout en Angular / Cypress**.
- Automatisation d'IHM pour sécuriser les mises en production.
- Tests unitaires en **Java**.
- Sprints, dailys, rétrospectives — Scrum en conditions réelles.

**Compétences validées** : continuité du service (les tests E2E ont littéralement pour but d'éviter les régressions en production), tests d'intégration et d'acceptation, planification Scrum, montée en compétence sur Cypress et Angular en autonomie. C'est le stage qui m'a fait passer du « je teste à la main quand j'y pense » à « je sécurise une mise en production avec une suite de tests automatisés ».

### Stage 2 — Janvier/Mars 2026 — Développeur Mainframe Junior

**Contexte** : développement et maintenance d'applications sur **mainframe IBM z/OS**, dans le domaine de la **tarification de services financiers**.

**Missions principales** :
- Développement **COBOL** sur z/OS.
- Évolution fonctionnelle du module de tarification de l'**Option Internationale** (cartes Visa Premier, Platinum, Infinite).
- Écriture de **scripts JCL** pour automatiser la génération de requêtes SQL en base **DB2**.
- Conception d'un **système automatisé de surveillance d'anomalies** avec alerting.

**Compétences validées** : exploitation de standards très contraints (COBOL/JCL/DB2 sur SI critique), continuité du service (impact direct sur la tarification bancaire en production), traitement de demandes d'évolution applicative, plans de tests, et surtout une **forte montée en compétence sur des technologies très spécialisées** que peu de jeunes développeurs touchent aujourd'hui.

Ce qui m'a marqué dans ces deux stages, c'est la différence d'**échelle** par rapport aux projets scolaires : on intervient sur un système où une régression n'est pas une mauvaise note, mais un incident en production qui bloque un service bancaire. Cette pression, je l'ai vraiment intégrée.

---

## 4. Conclusion (≈ 30 secondes)

Pour résumer : le BTS SIO m'a permis de passer d'**étudiant qui code** à **développeur qui livre un service**. À travers les sept projets de mon portfolio, et particulièrement les deux que je vous ai présentés en détail, j'ai validé l'ensemble des compétences du référentiel. Les deux stages à La Banque Postale ont confirmé mon orientation : je veux travailler sur des **applications de production, dans des environnements exigeants**, où la qualité du code a un impact réel.

Je poursuis maintenant mon parcours en **[bachelor / licence pro / école / alternance — à adapter]**, et je serais heureux de répondre à vos questions sur ce que je viens de présenter.

**Merci de votre attention.**

---

### Annexes — Aide-mémoire pour le jury (à ne PAS lire)

- **Projets cités** : AP 3.1 M2L Site Dynamique (PHP MVC, équipe de 3), AP 3.2 Aux Claviers Citoyens (consommation API REST OAuth2, client armée).
- **Autres projets disponibles si question** : AP 2.1 Générateur SQL (C#), AP 2.2 JPO (C#/Access), AP 2.3 M2L Statique (HTML/CSS), AP 2.4 Championnat (C#/MySQL), GEX (projet personnel finance/Python).
- **Stages** : LBP Tests Angular/Cypress (mai-juin 2025), LBP Mainframe COBOL/JCL/DB2 (janv-mars 2026).
- **Portfolio technique** : PHP MVC maison, SQLite, admin sécurisé (CSRF + sessions), publié sur GitHub.
