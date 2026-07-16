#include <iostream>

// Print the sum of the user input
int main()
{
    std::cout << "enter a set of number: ";

    int sum = 0;
    int number;

    while (std::cin >> number) {
        sum += number;
    }

    std::cout << "the sum is: " << sum << std::endl;

    return 0;
}
