#include <iostream>

using namespace std;

int main()
{
    // Change the value of pointer
    int *iptr = nullptr;
    int ival = 1337;
    iptr = &ival;

    cout << "ival: " << ival << " iptr: " << *iptr << endl;

    // Change the value of to which the pointer point
    *iptr = 67;

    cout << "ival: " << ival << endl;

    return 0;
}
