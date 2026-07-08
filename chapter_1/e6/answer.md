``` cc
std::cout << "The sum of " << v1;
          << " and " << v2;
          << " is " << v1 + v2 << std::endl;
```

the program fragment above is illegal, the programmer likely want to chain the output operator `<<` but each line is ended by semicolon `;` which indicate the end of statement, if we want to chain the multiple output operator we can simply delete the semicolon from the first and second line of code, then we can successfully chain it and make it into one statement. Alternatively we can add `std::cout` at the front of every line to make 3 separate statement.
