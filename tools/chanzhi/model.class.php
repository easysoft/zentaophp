    public function delete($table, $id)
    {
        $this->dao->delete()->from($table)->where('id')->eq($id)->exec();
    }
