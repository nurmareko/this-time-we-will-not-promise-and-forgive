#include <iostream>

int main()
{
    std::cout << "enter value for x and y" << std::endl << ">> ";

    int x, y;
    std::cin >> x >> y;

    if (x < y) {
        while (x <= y) {
            std::cout << x << std::endl;
            ++x;
        }
    } else if (y < x) {
        while (x >= y) {
            std::cout << x << std::endl;
            --x;
        }
    } else {
        std::cout << x << std::endl;
    }

    return 0;
}
