<form action="{{ url('/api/submissions') }}" method="POST">
    @csrf

    <label>Organization Name:</label>
    <input type="text" name="organization[name]" value="ITI" required>

    <label>Answer 1:</label>
    <input type="text" name="answers[q1]" value="A" required>

    <label>Answer 2:</label>
    <input type="text" name="answers[q2]" value="B" required>

    <label>Answer 3:</label>
    <input type="text" name="answers[q3]" value="C" required>

    <button type="submit">Submit</button>
</form>
