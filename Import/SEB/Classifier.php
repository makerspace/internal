<?php

class TransactionClassifier
{
	protected $rules;

	public function __construct()
	{
		$this->rules = json_decode(file_get_contents("rules.json"));
	}

	/**
	 *
	 */
	public function MatchRule($data)
	{
		$matches = [];
		foreach($this->rules as $rule)
		{
			$match = true;
			$m = false;

			// Match text
			if(isset($rule->match->text))
			{
				$match &= preg_match("/{$rule->match->text}/", $data["text"]);
				$m = true;
			}

			// Match reference number
			if(isset($rule->match->reference))
			{
				$match &= preg_match("/{$rule->match->reference}/", $data["reference"]);
				$m = true;
			}

			// Match to_account
			if(isset($rule->match->to_account) && isset($data["metadata"]["to"]["account"]))
			{
				$match &= preg_match("/{$rule->match->to_account}/", $data["metadata"]["to"]["account"]);
				$m = true;
			}

			// Match amount
			if(isset($rule->match->amount))
			{
				$match &= ($rule->match->amount == $data["amount"]);
				$m = true;
			}

			if($match && $m)
			{
				$matches[] = $rule;
			}
		}

		if(count($matches) == 0)
		{
			return false;
		}
		else
		{
			return $this->SelectRule($matches);
		}
	}

	/**
	 * Select the rule with the highest score
	 */
	public function SelectRule($rules)
	{
		// There is not much to do if there is only one rule
		if(count($rules) == 1)
		{
			return $rules[0];
		}

		// Go through the rules and get the one with highest score
		$last_rule = $rules[0];
		foreach($rules as $rule)
		{
			if($rule->score > $last_rule->score)
			{
				$last_rule = $rule;
			}
		}

		return $last_rule;
	}

	/**
	 *
	 */
	public function CreateInstruction($data)
	{

	}
}
