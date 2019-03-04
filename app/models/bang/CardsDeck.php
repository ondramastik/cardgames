<?php

namespace App\Models\Bang;


use Nette\InvalidStateException;

class CardsDeck {

    /** @var Card[] */
    private $cards;

    /** @var Character[] */
    private $characters;

    /** @var Role[] */
    private $roles;

    /** @var int */
    private $playersCount;

    /** @var Card[] */
    private $discardedCards;

    /** @var PlayedCard[] */
    private $playedCards;

    /**
     * CardsDeck constructor.
     * @param int $playersCount
     */
    public function __construct(int $playersCount) {
        $this->playersCount = $playersCount;
        $this->discardedCards = [];
        $this->playedCards = [];

        $this->roles = $this->initRoles();
        $this->cards = $this->initCards();
        $this->characters = $this->initCharacters();
    }

    /**
     * return Card
     */
    public function drawCard(): Card {
        $return = array_pop($this->cards);

        if($return === null) {
            $this->flipDiscardedCards();

            $return = array_pop($this->cards);
        }

        return $return;
    }
	
	/**
	 * @return Character
	 */
	public function drawCharacter(): Character {
		return array_pop($this->characters);
	}
	
	/**
	 * @return Role
	 */
	public function drawRole(): Role {
		return array_pop($this->roles);
	}

    /**
     * @return Card
     */
    public function drawFromDiscarded(): Card {
        return array_pop($this->discardedCards);
    }

    /**
     * @return PlayedCard
     */
    public function getActiveCard(): ?PlayedCard {
        for($i = count($this->getPlayedCards()) - 1; $i >= 0; $i--) {
            if(($this->getPlayedCards()[$i])->isActive()) {
                return $this->getPlayedCards()[$i];
            }
        }
        
        return null;
    }
	
	/**
	 * @return PlayedCard|null
	 */
	public function getTopPlayedCard(): ?PlayedCard  {
		return (end($this->playedCards) !== false ? end($this->playedCards) : null);
	}

	/**
	 * @return Card|null
	 */
	public function getTopDiscardedCard(): ?Card  {
		return (end($this->discardedCards) !== false ? end($this->discardedCards) : null);
	}

    public function disableActiveCard() {
        $this->getActiveCard()->setActive(false);
    }

    /**
     * @param Card $card
     */
    public function discardCard(Card $card) {
        array_push($this->discardedCards, $card);
    }

    /**
     * @param Card $card
     */
    public function return(Card $card) {
        array_push($this->cards, $card);
    }

    /**
     * @return PlayedCard[]
     */
    public function getPlayedCards(): array {
        return $this->playedCards;
    }

    /**
     * @param PlayedCard $playedCard
     */
    public function playCard(PlayedCard $playedCard) {
        array_push($this->playedCards, $playedCard);
    }

    private function flipDiscardedCards() {
        $topCard = array_pop($this->discardedCards);

        $cards = $this->discardedCards;

        shuffle($cards);

        $this->cards = $cards;

        $this->discardedCards = [$topCard];
    }

    /**
     * @return Role[]
     */
    private function initRoles(): array {
        if($this->playersCount === 4) {
            return [
                new Sceriffo(),
                new Rinnegato(),
                new Fuorilegge(),
                new Fuorilegge(),
            ];
        } else if($this->playersCount === 5) {
            return [
                new Sceriffo(),
                new Rinnegato(),
                new Fuorilegge(),
                new Fuorilegge(),
                new Vice(),
            ];
        } else if($this->playersCount === 6) {
            return [
                new Sceriffo(),
                new Rinnegato(),
                new Fuorilegge(),
                new Fuorilegge(),
                new Fuorilegge(),
                new Vice(),
            ];
        } else if($this->playersCount === 7) {
            return [
                new Sceriffo(),
                new Rinnegato(),
                new Fuorilegge(),
                new Fuorilegge(),
                new Fuorilegge(),
                new Vice(),
                new Vice(),
            ];
        } else throw new InvalidStateException();
    }

    /**
     * @return Card[]
     */
    private function initCards() : array {
        $cards = [
			new Bang(CardTypes::HEARTS, 'A'),
			new Bang(CardTypes::HEARTS, 'Q'),
			new Bang(CardTypes::HEARTS, 'K'),
			new Bang(CardTypes::TILES, 'A'),
			new Bang(CardTypes::TILES, '2'),
			new Bang(CardTypes::TILES, '3'),
			new Bang(CardTypes::TILES, '4'),
			new Bang(CardTypes::TILES, '5'),
			new Bang(CardTypes::TILES, '6'),
			new Bang(CardTypes::TILES, '7'),
			new Bang(CardTypes::TILES, '8'),
			new Bang(CardTypes::TILES, '9'),
			new Bang(CardTypes::TILES, '10'),
			new Bang(CardTypes::TILES, 'J'),
			new Bang(CardTypes::TILES, 'Q'),
			new Bang(CardTypes::TILES, 'K'),
			new Bang(CardTypes::CLOVERS, '2'),
			new Bang(CardTypes::CLOVERS, '3'),
			new Bang(CardTypes::CLOVERS, '4'),
			new Bang(CardTypes::CLOVERS, '5'),
			new Bang(CardTypes::CLOVERS, '6'),
			new Bang(CardTypes::CLOVERS, '7'),
			new Bang(CardTypes::CLOVERS, '8'),
			new Bang(CardTypes::CLOVERS, '9'),
        	new Bang(CardTypes::PIKES, 'A'),
			new Mancato(CardTypes::CLOVERS, 'A'),
			new Mancato(CardTypes::CLOVERS, '10'),
			new Mancato(CardTypes::CLOVERS, 'J'),
			new Mancato(CardTypes::CLOVERS, 'Q'),
			new Mancato(CardTypes::CLOVERS, 'K'),
			new Mancato(CardTypes::PIKES, '2'),
			new Mancato(CardTypes::PIKES, '3'),
			new Mancato(CardTypes::PIKES, '4'),
			new Mancato(CardTypes::PIKES, '5'),
			new Mancato(CardTypes::PIKES, '6'),
			new Mancato(CardTypes::PIKES, '7'),
			new Mancato(CardTypes::PIKES, '8'),
			new Birra(CardTypes::HEARTS, '6'),
			new Birra(CardTypes::HEARTS, '7'),
			new Birra(CardTypes::HEARTS, '8'),
			new Birra(CardTypes::HEARTS, '9'),
			new Birra(CardTypes::HEARTS, '10'),
			new Birra(CardTypes::HEARTS, 'J'),
			new Panico(CardTypes::HEARTS, 'A'),
			new Panico(CardTypes::HEARTS, 'J'),
			new Panico(CardTypes::HEARTS, 'Q'),
			new Panico(CardTypes::TILES, '8'),
			new Indiani(CardTypes::TILES, 'A'),
			new Indiani(CardTypes::TILES, 'K'),
			new Duello(CardTypes::TILES, 'Q'),
			new Duello(CardTypes::CLOVERS, '8'),
			new Duello(CardTypes::PIKES, 'J'),
			new CatBalou(CardTypes::HEARTS, 'K'),
			new CatBalou(CardTypes::TILES, '9'),
			new CatBalou(CardTypes::TILES, '10'),
			new CatBalou(CardTypes::TILES, 'J'),
			new Diligenza(CardTypes::PIKES, 'J'),
			new Diligenza(CardTypes::PIKES, 'J'),
			new WellsFargo(CardTypes::HEARTS, '3'),
			new Emporio(CardTypes::CLOVERS, '9'),
			new Emporio(CardTypes::PIKES, 'Q'),
			new Gatling(CardTypes::HEARTS, '10'),
			new Saloon(CardTypes::HEARTS, '5'),
			new Prigione(CardTypes::HEARTS, '4'),
			new Prigione(CardTypes::PIKES, '10'),
			new Prigione(CardTypes::PIKES, 'J'),
			new Mustang(CardTypes::HEARTS, '8'),
			new Mustang(CardTypes::HEARTS, '9'),
			new Barile(CardTypes::PIKES, 'Q'),
			new Barile(CardTypes::PIKES, 'K'),
			new Dinamite(CardTypes::HEARTS, '2'),
			new Appaloosa(CardTypes::PIKES, 'A'),
			new Volcanic(CardTypes::CLOVERS, '10'),
			new Volcanic(CardTypes::PIKES, '10'),
			new Schofield(CardTypes::CLOVERS, 'J'),
			new Schofield(CardTypes::CLOVERS, 'Q'),
			new Schofield(CardTypes::PIKES, 'K'),
			new Remington(CardTypes::CLOVERS, 'K'),
			new RevCarabine(CardTypes::CLOVERS, 'A'),
			new Winchester(CardTypes::PIKES, '8'),
		];

        shuffle($cards);

        return $cards;
    }
	
	/**
	 * @return Character[]
	 */
    private function initCharacters() : array {
    	$characters = [
    		new BartCassidy(),
			new BlackJack(),
			new CalamityJanet(),
			new ElGringo(),
			new JesseJones(),
			new Jourdonnais(),
			new KitCarlson(),
			new LuckyDuke(),
			new PaulRegret(),
			new PedroRamirez(),
			new RoseDoolan(),
			new SidKetchum(),
			new SlabTheKiller(),
			new SuzyLafayette(),
			new VultureSam(),
			new WillyTheKid(),
		];
    	
    	shuffle($characters);
    	
    	return $characters;
	}

}