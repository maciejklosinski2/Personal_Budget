<?php

namespace App\Models;

use PDO;
use \Core\View;
use \App\Flash;

class Income extends \Core\Model
{
	public $amountErrors = [];
	public $dateErrors = [];

	public function __construct( $data = [] ) 
	{
        foreach ( $data as $key => $value ) 
        {
            $this->$key = $value;
        }
    }

    public function getUserCategoryId($user_id) 
    {
        $sql = 'SELECT id, user_id, name FROM incomes_category_assigned_to_users WHERE user_id = :user_id AND name = :name';

        $db = static::getDB();

        $stmt = $db->prepare($sql);
        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $this->category, PDO::PARAM_STR );
        $stmt->execute();

        $categories = $stmt -> fetch();

        return $categories['id'];
    }    

    public function saveUserIncome($user_id) 
    {
        $this->amount = str_replace( [','], ['.'], $this->amount );
        $this->amountErrors = Flash::validateAmount( $this->amount );
        $this->dateErrors = Flash::validateDate( $this->date );

        if((empty($this->amountErrors)) && (empty($this->dateErrors))) 
        {
            $sql = 'INSERT INTO incomes VALUES(NULL, :user_id, :income_category_assigned_to_user_id, :amount, :date_of_income, :income_comment)';

            $db = static::getDB();
            $stmt = $db->prepare( $sql );
            $comment = filter_var( $this->comment, FILTER_SANITIZE_SPECIAL_CHARS);

            $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
            $stmt->bindValue( ':income_category_assigned_to_user_id', $this->getUserCategoryId($user_id), PDO::PARAM_INT );
            $stmt->bindValue( ':amount', $this->amount, PDO::PARAM_STR );
            $stmt->bindValue( ':date_of_income', $this->date, PDO::PARAM_STR );
            $stmt->bindValue( ':income_comment', $comment, PDO::PARAM_STR );

            return $stmt->execute();
        }
        return false;
    }

    public static function getUserIncomeCategories($id) 
    {
        $db = static::getDB();

        $query_income_categories=$db->prepare("SELECT id, name FROM incomes_category_assigned_to_users WHERE user_id=:id");

        $query_income_categories->bindValue(':id',$_SESSION['user_id'],PDO::PARAM_INT);
        $query_income_categories->execute();
        $income_categories=$query_income_categories->fetchAll();        

        return $income_categories;
        
    }
}