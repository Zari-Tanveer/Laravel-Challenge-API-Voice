public function voice(Request $request){
    $request->validate([
        'question_id'=>'required|int|exists:questions,id',
        'value'=>'required|boolean',
    ]);
    $question=Question::find($request->post('question_id'));
    abort_if($question->user_id==auth()->id(),500,'The user is not allowed to vote to your question');
    //check if user voted 
    $voice = Voice::firstOrCreate(
        [
            'user_id' => auth()->id(),
            'question_id' => $request->post('question_id')
        ],
        [
            'value' => $request->post('value')
        ]
    );
    <!-- check vote recently created -->
    if($voice->wasRecentlyCreated){
        return [
            'message'=>'Voting completed successfully'
        ];
    }
    if ($voice->value===$request->post('value')) {
        return response()->json([
            'message' => 'The user is not allowed to vote more than once'
        ],500);
    }else{
        $voice->update([
            'value'=>$request->post('value')
        ]);
        return response()->json([
            'message'=>'update your voice'
        ],201);
    }
}